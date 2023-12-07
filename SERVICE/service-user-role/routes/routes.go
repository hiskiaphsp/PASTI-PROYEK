package routes

import (
	"github.com/hiskiaphsp/service-user-role/controllers"
	"github.com/hiskiaphsp/service-user-role/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	userRoleRepository := repositories.UserRoleRepository{}
	userRoleController := controllers.UserRoleController{
		UserRoleRepository: &userRoleRepository,
	}

	e.POST("/user-roles", userRoleController.CreateUserRole)
	e.GET("/user-roles", userRoleController.GetAllUserRoles)
	e.GET("/user-roles/:id", userRoleController.GetUserRoleByID)
	e.PUT("/user-roles/:id", userRoleController.UpdateUserRole)
	e.DELETE("/user-roles/:id", userRoleController.DeleteUserRole)
	e.GET("/user-role/:userID", userRoleController.GetUserRoleByUserID)

	return e
}
