package routes

import (
	"github.com/hiskiaphsp/service-notification/controllers"
	"github.com/hiskiaphsp/service-notification/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	notificationRepository := repositories.NotificationRepository{}
	notificationController := controllers.NotificationController{
		NotificationRepository: &notificationRepository,
	}

	e.POST("/notifications", notificationController.CreateNotification)
	e.GET("/notifications", notificationController.GetAllNotifications)
	e.GET("/notifications/:id", notificationController.GetNotificationByID)
	e.PUT("/notifications/:id", notificationController.UpdateNotification)
	e.DELETE("/notifications/:id", notificationController.DeleteNotification)

	return e
}
