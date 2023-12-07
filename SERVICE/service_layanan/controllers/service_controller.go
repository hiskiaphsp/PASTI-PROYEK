package controllers

import (
	"log"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-service/models"
	"github.com/hiskiaphsp/service-service/repositories"
	"github.com/labstack/echo/v4"
)

type ServiceController struct {
	ServiceRepository *repositories.ServiceRepository
}


func (c *ServiceController) CreateService(ctx echo.Context) error {
	service := new(models.Service)
	if err := ctx.Bind(service); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	service.CreatedAt = time.Now().Unix()

	err := c.ServiceRepository.Create(service)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create service")
	}

	return ctx.JSON(http.StatusCreated, service)
}


func (c *ServiceController) GetAllServices(ctx echo.Context) error {
	services, err := c.ServiceRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get services")
	}

	return ctx.JSON(http.StatusOK, services)
}

func (c *ServiceController) GetServiceByID(ctx echo.Context) error {
	id := ctx.Param("id")

	service, err := c.ServiceRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Service not found")
	}

	return ctx.JSON(http.StatusOK, service)
}

func (c *ServiceController) UpdateService(ctx echo.Context) error {
	id := ctx.Param("id")

	// Mengambil data service sebelumnya dari database
	previousService, err := c.ServiceRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get service")
	}

	service := new(models.Service)
	if err := ctx.Bind(service); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Mengganti nilai created_at dengan nilai sebelumnya
	service.CreatedAt = previousService.CreatedAt

	err = c.ServiceRepository.Update(id, service)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update service")
	}

	return ctx.JSON(http.StatusOK, service)
}


func (c *ServiceController) DeleteService(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.ServiceRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete service")
	}

	return ctx.NoContent(http.StatusNoContent)
}
