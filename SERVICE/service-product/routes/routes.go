package routes

import (
	"github.com/hiskiaphsp/service-product/controllers"
	"github.com/hiskiaphsp/service-product/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	productRepository := repositories.ProductRepository{}
	productController := controllers.ProductController{
		ProductRepository: &productRepository,
	}

	e.POST("/products", productController.CreateProduct)
	e.GET("/products", productController.GetAllProducts)
	e.GET("/products/:id", productController.GetProductByID)
	e.PUT("/products/:id", productController.UpdateProduct)
	e.DELETE("/products/:id", productController.DeleteProduct)

	return e
}
