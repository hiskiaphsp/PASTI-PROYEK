package routes

import (
	"github.com/hiskiaphsp/service-user/controllers"
	"github.com/hiskiaphsp/service-user/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	userRepository := repositories.UserRepository{}
	userController := controllers.UserController{
		UserRepository: &userRepository,
	}

	e.POST("/users", userController.CreateUser)
	e.GET("/users", userController.GetAllUsers)
	e.GET("/users/:id", userController.GetUserByID)
	e.PUT("/users/:id", userController.UpdateUser)
	e.DELETE("/users/:id", userController.DeleteUser)
	e.POST("/login", userController.Login)
	e.POST("/register", userController.Register)
	e.GET("/logout", userController.Logout)
	e.GET("/user", userController.GetUserDataFromToken)

	return e
}
