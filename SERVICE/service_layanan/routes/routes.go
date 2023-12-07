package routes

import (
	"github.com/hiskiaphsp/service-service/controllers"
	"github.com/hiskiaphsp/service-service/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	serviceRepository := repositories.ServiceRepository{}
	serviceController := controllers.ServiceController{
		ServiceRepository: &serviceRepository,
	}

	e.POST("/services", serviceController.CreateService)
	e.GET("/services", serviceController.GetAllServices)
	e.GET("/services/:id", serviceController.GetServiceByID)
	e.PUT("/services/:id", serviceController.UpdateService)
	e.DELETE("/services/:id", serviceController.DeleteService)

	return e
}
