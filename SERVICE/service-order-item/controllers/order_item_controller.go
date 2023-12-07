package controllers

import (
	"log"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-order-item/models"
	"github.com/hiskiaphsp/service-order-item/repositories"
	"github.com/labstack/echo/v4"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type OrderItemController struct {
	OrderItemRepository *repositories.OrderItemRepository
}

func (c *OrderItemController) CreateOrderItem(ctx echo.Context) error {
	orderItem := new(models.OrderItem)
	if err := ctx.Bind(orderItem); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	orderItem.CreatedAt = time.Now().Unix()

	err := c.OrderItemRepository.Create(orderItem)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create order item")
	}

	return ctx.JSON(http.StatusCreated, orderItem)
}

func (c *OrderItemController) GetAllOrderItems(ctx echo.Context) error {
	orderItems, err := c.OrderItemRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get order items")
	}

	return ctx.JSON(http.StatusOK, orderItems)
}

func (c *OrderItemController) GetOrderItemByID(ctx echo.Context) error {
	id := ctx.Param("id")

	orderItem, err := c.OrderItemRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Order item not found")
	}

	return ctx.JSON(http.StatusOK, orderItem)
}

func (c *OrderItemController) GetOrderItemsByOrderID(ctx echo.Context) error {
	orderIDParam := ctx.Param("orderID")

	orderID, err := primitive.ObjectIDFromHex(orderIDParam)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusBadRequest, "OrderID tidak valid")
	}

	orderItems, err := c.OrderItemRepository.GetByOrderID(orderID)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Gagal mengambil order items")
	}

	return ctx.JSON(http.StatusOK, orderItems)
}

func (c *OrderItemController) UpdateOrderItem(ctx echo.Context) error {
	id := ctx.Param("id")

	// Get the previous order item data from the database
	previousOrderItem, err := c.OrderItemRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get order item")
	}

	orderItem := new(models.OrderItem)
	if err := ctx.Bind(orderItem); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Set the created_at value to the previous value
	orderItem.CreatedAt = previousOrderItem.CreatedAt

	err = c.OrderItemRepository.Update(id, orderItem)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update order item")
	}

	return ctx.JSON(http.StatusOK, orderItem)
}

func (c *OrderItemController) DeleteOrderItem(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.OrderItemRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete order item")
	}

	return ctx.NoContent(http.StatusNoContent)
}
