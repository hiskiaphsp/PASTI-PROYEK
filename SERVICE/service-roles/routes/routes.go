package routes

import (
	"github.com/hiskiaphsp/service-role/controllers"
	"github.com/hiskiaphsp/service-role/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	roleRepository := repositories.RoleRepository{}
	roleController := controllers.RoleController{
		RoleRepository: &roleRepository,
	}

	e.POST("/roles", roleController.CreateRole)
	e.GET("/roles", roleController.GetAllRoles)
	e.GET("/roles/:id", roleController.GetRoleByID)
	e.PUT("/roles/:id", roleController.UpdateRole)
	e.DELETE("/roles/:id", roleController.DeleteRole)

	return e
}
