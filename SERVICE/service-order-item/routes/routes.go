package routes

import (
	"github.com/hiskiaphsp/service-order-item/controllers"
	"github.com/hiskiaphsp/service-order-item/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	orderItemRepository := repositories.OrderItemRepository{}
	orderItemController := controllers.OrderItemController{
		OrderItemRepository: &orderItemRepository,
	}

	e.POST("/order-items", orderItemController.CreateOrderItem)
	e.GET("/order-items", orderItemController.GetAllOrderItems)
	e.GET("/order-items/:id", orderItemController.GetOrderItemByID)
	e.PUT("/order-items/:id", orderItemController.UpdateOrderItem)
	e.DELETE("/order-items/:id", orderItemController.DeleteOrderItem)
	e.GET("/order-item/:orderID", orderItemController.GetOrderItemsByOrderID)

	return e
}
