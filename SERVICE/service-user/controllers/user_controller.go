package controllers

import (
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"strings"
	"time"

	"github.com/dgrijalva/jwt-go"
	"github.com/hiskiaphsp/service-user/models"
	"github.com/hiskiaphsp/service-user/repositories"
	"github.com/labstack/echo/v4"
)

type UserController struct {
	UserRepository *repositories.UserRepository
}

func (c *UserController) GetUserDataFromToken(ctx echo.Context) error {
	// Mendapatkan token dari header permintaan
	authHeader := ctx.Request().Header.Get("Authorization")
	tokenString := strings.Replace(authHeader, "Bearer ", "", 1)

	// Mendekode token
	token, err := jwt.Parse(tokenString, func(token *jwt.Token) (interface{}, error) {
		// Pastikan bahwa metode tanda tangan token adalah HMAC dengan tipe tanda tangan yang sama dengan yang Anda gunakan saat membuat token
		if _, ok := token.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("unexpected signing method: %v", token.Header["alg"])
		}

		// Kembalikan kunci rahasia yang sama yang Anda gunakan saat membuat token
		return []byte("n4dXax3Q1Xu71yB3hpMTJUtyyyjPFfkkHie66ft7JIdfCLUxHi5Z5inUogwTsQce"), nil
	})

	// Penanganan kesalahan saat mendekode token
	if err != nil {
		return ctx.JSON(http.StatusUnauthorized, map[string]interface{}{
			"error": "Failed to decode token",
		})
	}

	// Memeriksa apakah token valid
	if _, ok := token.Claims.(jwt.MapClaims); !ok || !token.Valid {
		return ctx.JSON(http.StatusUnauthorized, map[string]interface{}{
			"error": "Invalid token",
			"token": tokenString,
		})
	}

	// Mendapatkan data pengguna dari token
	claims, ok := token.Claims.(jwt.MapClaims)
	if !ok {
		return ctx.JSON(http.StatusUnauthorized, map[string]interface{}{
			"error": "Failed to get user data from token",
		})
	}

	// Ambil nilai-nilai yang diinginkan dari klaim
	userID, _ := claims["userID"].(string)
	name, _ := claims["name"].(string)
	email, _ := claims["email"].(string)

	// Gunakan data pengguna sesuai kebutuhan
	// ...

	response := map[string]interface{}{
		"userID": userID,
		"name":   name,
		"email":  email,
	}

	return ctx.JSON(http.StatusOK, response)
}
func (c *UserController) Login(ctx echo.Context) error {
    // Baca body permintaan secara manual
    body, err := ioutil.ReadAll(ctx.Request().Body)
    if err != nil {
        log.Println(err)
        return ctx.JSON(http.StatusBadRequest, map[string]interface{}{
            "error": "Failed to read request body",
        })
    }

    // Parsing data dari body sebagai JSON
    var loginData struct {
        Email    string `json:"email"`
        Password string `json:"password"`
    }
    err = json.Unmarshal(body, &loginData)
    if err != nil {
        log.Println(err)
        return ctx.JSON(http.StatusBadRequest, map[string]interface{}{
            "error": "Invalid JSON data",
        })
    }

    email := loginData.Email
    password := loginData.Password

    // Dapatkan data pengguna dari MongoDB berdasarkan email
    user, err := c.UserRepository.GetByEmail(email)
    if err != nil {
        if err == repositories.ErrUserNotFound {
            return ctx.JSON(http.StatusUnauthorized, map[string]interface{}{
                "error": "Unauthorized",
            })
        }
        log.Println(err)
        return ctx.JSON(http.StatusInternalServerError, map[string]interface{}{
            "error": "Failed to authenticate user",
        })
    }

    // Verifikasi password
    if !verifyPassword(password, user.Password) {
        return ctx.JSON(http.StatusUnauthorized, map[string]interface{}{
            "error": "Unauthorized",
        })
    }

    // Buat token JWT dengan data pengguna
    tokenString, err := generateToken(user.ID.Hex(), user.Name, user.Email)
    if err != nil {
        log.Println(err)
        return ctx.JSON(http.StatusInternalServerError, map[string]interface{}{
            "error": "Failed to generate token",
        })
    }

    // Mengembalikan data pengguna dalam respons
    response := map[string]interface{}{
        "message": "Login successful",
        "token":   tokenString,
        "user":    user,
    }

    return ctx.JSON(http.StatusOK, response)
}


func generateToken(userID, name, email string) (string, error) {
	// Membuat claims JWT
	claims := jwt.MapClaims{
		"userID": userID,
		"name":   name,
		"email":  email,
		"exp":    time.Now().Add(time.Hour * 24).Unix(), // Token berlaku selama 1 hari
	}

	// Membuat token dengan menggunakan secret key
	secretKey := []byte("n4dXax3Q1Xu71yB3hpMTJUtyyyjPFfkkHie66ft7JIdfCLUxHi5Z5inUogwTsQce") // Ganti dengan kunci rahasia Anda
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	tokenString, err := token.SignedString(secretKey)
	if err != nil {
		return "", err
	}

	// Menambahkan skema "Bearer" ke token JWT
	tokenString = "Bearer " + tokenString

	return tokenString, nil
}


func verifyPassword(password string, storedPassword string) bool {
    return password == storedPassword
}

func (c *UserController) Logout(ctx echo.Context) error {
	// Hapus token dari cookie
	clearAuthCookie(ctx)

	return ctx.JSON(http.StatusOK, map[string]interface{}{
		"message": "Logout successful",
	})
}
func isAuthenticated(ctx echo.Context) bool {
	token := ctx.Request().Header.Get("Authorization")
	if token == "" {
		return false
	}

	// Menghilangkan skema "Bearer " dari token
	token = strings.TrimPrefix(token, "Bearer ")

	// Memeriksa keabsahan token
	secretKey := "17" // Ganti dengan kunci rahasia Anda
	parsedToken, err := jwt.Parse(token, func(token *jwt.Token) (interface{}, error) {
		return []byte(secretKey), nil
	})

	if err != nil || !parsedToken.Valid {
		return false
	}

	return true
}


func (c *UserController) Register(ctx echo.Context) error {
    user := new(models.User)
    if err := ctx.Bind(user); err != nil {
        return echo.NewHTTPError(http.StatusBadRequest, err.Error())
    }

    // Mengecek apakah email sudah terdaftar sebelumnya
    existingUser, err := c.UserRepository.GetByEmail(user.Email)
    if err != nil && !errors.Is(err, repositories.ErrUserNotFound) {
        log.Println(err)
        return echo.NewHTTPError(http.StatusInternalServerError, "Failed to register user")
    }
    if existingUser != nil {
        return echo.NewHTTPError(http.StatusConflict, "Email is already registered")
    }

    user.CreatedAt = time.Now().Unix()

    err = c.UserRepository.Create(user)
    if err != nil {
        log.Println(err)
        return echo.NewHTTPError(http.StatusInternalServerError, "Failed to register user")
    }

    // Generate token
    token, err := generateToken(user.ID.Hex(), user.Name, user.Email)
    if err != nil {
        log.Println(err)
        return echo.NewHTTPError(http.StatusInternalServerError, "Failed to register user")
    }

    // Mengirim token dalam respons
    response := map[string]interface{}{
        "user":  user,
        "token": token,
    }

    return ctx.JSON(http.StatusCreated, response)
}


func setAuthCookie(ctx echo.Context, tokenString string) {
	cookie := new(http.Cookie)
	cookie.Name = "auth_token"
	cookie.Value = tokenString
	cookie.Expires = time.Now().Add(time.Hour * 24) // Set waktu kadaluarsa cookie
	cookie.HttpOnly = true                          // Cookie hanya dapat diakses melalui HTTP
	cookie.Secure = true                             // Cookie hanya dikirim melalui koneksi HTTPS
	// Set path sesuai dengan kebutuhan aplikasi
	cookie.Path = "/"
	ctx.SetCookie(cookie)
}

func clearAuthCookie(ctx echo.Context) {
	cookie := new(http.Cookie)
	cookie.Name = "auth_token"
	cookie.Value = ""
	cookie.Expires = time.Now().Add(-time.Hour)
	cookie.HttpOnly = true
	cookie.Secure = true
	cookie.Path = "/"
	ctx.SetCookie(cookie)
}


func (c *UserController) CreateUser(ctx echo.Context) error {
    user := new(models.User)
    if err := ctx.Bind(user); err != nil {
        return echo.NewHTTPError(http.StatusBadRequest, err.Error())
    }

    user.CreatedAt = time.Now().Unix()

    err := c.UserRepository.Create(user)
    if err != nil {
        log.Println(err)
        return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create user")
    }

    return ctx.JSON(http.StatusCreated, user)
}




func (c *UserController) GetAllUsers(ctx echo.Context) error {
	users, err := c.UserRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get users")
	}

	return ctx.JSON(http.StatusOK, users)
}

func (c *UserController) GetUserByID(ctx echo.Context) error {
	id := ctx.Param("id")

	user, err := c.UserRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "User not found")
	}

	return ctx.JSON(http.StatusOK, user)
}

func (c *UserController) UpdateUser(ctx echo.Context) error {
	id := ctx.Param("id")

	// Mengambil data user sebelumnya dari database
	previousUser, err := c.UserRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get user")
	}

	user := new(models.User)
	if err := ctx.Bind(user); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Mengganti nilai created_at dengan nilai sebelumnya
	user.CreatedAt = previousUser.CreatedAt

	err = c.UserRepository.Update(id, user)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update user")
	}

	return ctx.JSON(http.StatusOK, user)
}


func (c *UserController) DeleteUser(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.UserRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete user")
	}

	return ctx.NoContent(http.StatusNoContent)
}
