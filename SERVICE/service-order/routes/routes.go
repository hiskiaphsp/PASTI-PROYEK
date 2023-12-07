package routes

import (
	"github.com/hiskiaphsp/service-order/controllers"
	"github.com/hiskiaphsp/service-order/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	orderRepository := repositories.OrderRepository{}
	orderController := controllers.OrderController{
		OrderRepository: &orderRepository,
	}

	e.POST("/orders", orderController.CreateOrder)
	e.GET("/orders", orderController.GetAllOrders)
	e.GET("/orders/:id", orderController.GetOrderByID)
	e.PUT("/orders/:id", orderController.UpdateOrder)
	e.DELETE("/orders/:id", orderController.DeleteOrder)
	e.PUT("/orders/:id/status", orderController.UpdateOrderStatus)

	return e
}
