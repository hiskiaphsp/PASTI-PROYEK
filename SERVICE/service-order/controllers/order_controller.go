package controllers

import (
	"log"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-order/models"
	"github.com/hiskiaphsp/service-order/repositories"
	"github.com/labstack/echo/v4"
)

type OrderController struct {
	OrderRepository *repositories.OrderRepository
}

func (c *OrderController) CreateOrder(ctx echo.Context) error {
	order := new(models.Order)
	if err := ctx.Bind(order); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	order.CreatedAt = time.Now().Unix() // Assign created_at based on current Unix timestamp

	err := c.OrderRepository.Create(order)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create order")
	}

	return ctx.JSON(http.StatusCreated, order)
}

func (c *OrderController) GetAllOrders(ctx echo.Context) error {
	orders, err := c.OrderRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get orders")
	}

	return ctx.JSON(http.StatusOK, orders)
}

func (c *OrderController) GetOrderByID(ctx echo.Context) error {
	id := ctx.Param("id")

	order, err := c.OrderRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Order not found")
	}

	return ctx.JSON(http.StatusOK, order)
}

func (c *OrderController) UpdateOrder(ctx echo.Context) error {
	id := ctx.Param("id")

	// Get the existing order data
	existingOrder, err := c.OrderRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Order not found")
	}

	order := new(models.Order)
	if err := ctx.Bind(order); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Use the existing created_at
	order.CreatedAt = existingOrder.CreatedAt

	err = c.OrderRepository.Update(id, order)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update order")
	}

	return ctx.JSON(http.StatusOK, order)
}

func (c *OrderController) UpdateOrderStatus(ctx echo.Context) error {
	id := ctx.Param("id")

	// Get the existing order data
	existingOrder, err := c.OrderRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Order not found")
	}

	order := new(models.Order)
	if err := ctx.Bind(order); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Update only the order status
	existingOrder.OrderStatus = order.OrderStatus

	err = c.OrderRepository.UpdateStatus(id, existingOrder.OrderStatus)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update order status")
	}

	return ctx.JSON(http.StatusOK, existingOrder)
}

func (c *OrderController) DeleteOrder(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.OrderRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete order")
	}

	return ctx.NoContent(http.StatusNoContent)
}
