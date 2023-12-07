package controllers

import (
	"log"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-product/models"
	"github.com/hiskiaphsp/service-product/repositories"
	"github.com/labstack/echo/v4"
)

type ProductController struct {
	ProductRepository *repositories.ProductRepository
}

func (c *ProductController) CreateProduct(ctx echo.Context) error {
	product := new(models.Product)
	if err := ctx.Bind(product); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	product.CreatedAt = time.Now().Unix()

	err := c.ProductRepository.Create(product)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create product")
	}

	return ctx.JSON(http.StatusCreated, product)
}

func (c *ProductController) GetAllProducts(ctx echo.Context) error {
	products, err := c.ProductRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get products")
	}

	return ctx.JSON(http.StatusOK, products)
}

func (c *ProductController) GetProductByID(ctx echo.Context) error {
	id := ctx.Param("id")

	product, err := c.ProductRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Product not found")
	}

	return ctx.JSON(http.StatusOK, product)
}

func (c *ProductController) UpdateProduct(ctx echo.Context) error {
	id := ctx.Param("id")

	// Get the previous product data from the database
	previousProduct, err := c.ProductRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get product")
	}

	product := new(models.Product)
	if err := ctx.Bind(product); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Set the created_at value to the previous value
	product.CreatedAt = previousProduct.CreatedAt

	err = c.ProductRepository.Update(id, product)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update product")
	}

	return ctx.JSON(http.StatusOK, product)
}

func (c *ProductController) DeleteProduct(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.ProductRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete product")
	}

	return ctx.NoContent(http.StatusNoContent)
}
