package controllers

import (
	"log"
	"net/http"

	"github.com/hiskiaphsp/service-notification/models"
	"github.com/hiskiaphsp/service-notification/repositories"
	"github.com/labstack/echo/v4"
)

type NotificationController struct {
	NotificationRepository *repositories.NotificationRepository
}

func (c *NotificationController) CreateNotification(ctx echo.Context) error {
	notification := new(models.Notification)
	if err := ctx.Bind(notification); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	err := c.NotificationRepository.Create(notification)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create notification")
	}

	return ctx.JSON(http.StatusCreated, notification)
}

func (c *NotificationController) GetAllNotifications(ctx echo.Context) error {
	notifications, err := c.NotificationRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get notifications")
	}

	return ctx.JSON(http.StatusOK, notifications)
}

func (c *NotificationController) GetNotificationByID(ctx echo.Context) error {
	id := ctx.Param("id")

	notification, err := c.NotificationRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Notification not found")
	}

	return ctx.JSON(http.StatusOK, notification)
}

func (c *NotificationController) UpdateNotification(ctx echo.Context) error {
	id := ctx.Param("id")

	notification := new(models.Notification)
	if err := ctx.Bind(notification); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	err := c.NotificationRepository.Update(id, notification)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update notification")
	}

	return ctx.JSON(http.StatusOK, notification)
}

func (c *NotificationController) DeleteNotification(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.NotificationRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete notification")
	}

	return ctx.NoContent(http.StatusNoContent)
}
