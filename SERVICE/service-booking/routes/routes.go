package routes

import (
	"github.com/hiskiaphsp/service-booking/controllers"
	"github.com/hiskiaphsp/service-booking/repositories"
	"github.com/labstack/echo/v4"
)

func InitRoutes() *echo.Echo {
	e := echo.New()

	bookingRepository := repositories.BookingRepository{}
	bookingController := controllers.BookingController{
		BookingRepository: &bookingRepository,
	}

	e.POST("/bookings", bookingController.CreateBooking)
	e.GET("/bookings", bookingController.GetAllBookings)
	e.GET("/bookings/:id", bookingController.GetBookingByID)
	e.PUT("/bookings/:id", bookingController.UpdateBooking)
	e.DELETE("/bookings/:id", bookingController.DeleteBooking)
	e.PUT("/bookings/:id/status", bookingController.UpdateBookingStatus)


	return e
}
