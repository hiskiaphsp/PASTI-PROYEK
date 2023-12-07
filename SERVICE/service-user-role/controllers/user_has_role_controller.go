package controllers

import (
	"log"
	"net/http"

	"github.com/hiskiaphsp/service-user-role/models"
	"github.com/hiskiaphsp/service-user-role/repositories"
	"github.com/labstack/echo/v4"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type UserRoleController struct {
	UserRoleRepository *repositories.UserRoleRepository
}

func (c *UserRoleController) CreateUserRole(ctx echo.Context) error {
	userRole := new(models.UserRole)
	if err := ctx.Bind(userRole); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	err := c.UserRoleRepository.Create(userRole)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create user role")
	}

	return ctx.JSON(http.StatusCreated, userRole)
}



func (c *UserRoleController) GetAllUserRoles(ctx echo.Context) error {
	userRoles, err := c.UserRoleRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get user roles")
	}

	return ctx.JSON(http.StatusOK, userRoles)
}

func (c *UserRoleController) GetUserRoleByID(ctx echo.Context) error {
	id := ctx.Param("id")

	userRole, err := c.UserRoleRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "User role not found")
	}

	return ctx.JSON(http.StatusOK, userRole)
}

func (c *UserRoleController) GetUserRoleByUserID(ctx echo.Context) error {
	userID := ctx.Param("userID")

	// Mengubah userID menjadi tipe primitive.ObjectID
	objectID, err := primitive.ObjectIDFromHex(userID)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusBadRequest, "Invalid user ID")
	}

	userRole, err := c.UserRoleRepository.GetByUserID(objectID.Hex()) // Menggunakan objectID.Hex() untuk mendapatkan string dari primitive.ObjectID
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "User role not found")
	}

	return ctx.JSON(http.StatusOK, userRole)
}




func (c *UserRoleController) UpdateUserRole(ctx echo.Context) error {
	id := ctx.Param("id")

	userRole := new(models.UserRole)
	if err := ctx.Bind(userRole); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	err := c.UserRoleRepository.Update(id, userRole)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update user role")
	}

	return ctx.JSON(http.StatusOK, userRole)
}


func (c *UserRoleController) DeleteUserRole(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.UserRoleRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete user role")
	}

	return ctx.NoContent(http.StatusNoContent)
}
