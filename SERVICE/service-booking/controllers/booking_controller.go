package controllers

import (
	"log"
	"math/rand"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-booking/models"
	"github.com/hiskiaphsp/service-booking/repositories"
	"github.com/labstack/echo/v4"
)

type BookingController struct {
	BookingRepository *repositories.BookingRepository
}


func (c *BookingController) CreateBooking(ctx echo.Context) error {
	booking := new(models.Booking)
	if err := ctx.Bind(booking); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	booking.CreatedAt = time.Now().Unix() // Assign created_at based on current Unix timestamp

	// Generate random booking code
	booking.BookingCode = generateBookingCode(8)

	err := c.BookingRepository.Create(booking)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create booking")
	}

	return ctx.JSON(http.StatusCreated, booking)
}

// Function to generate random booking code
func generateBookingCode(length int) string {
	charset := "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
	seededRand := rand.New(rand.NewSource(time.Now().UnixNano()))

	b := make([]byte, length)
	for i := range b {
		b[i] = charset[seededRand.Intn(len(charset))]
	}

	return string(b)
}

func (c *BookingController) GetAllBookings(ctx echo.Context) error {
	bookings, err := c.BookingRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get bookings")
	}

	return ctx.JSON(http.StatusOK, bookings)
}

func (c *BookingController) GetBookingByID(ctx echo.Context) error {
	id := ctx.Param("id")

	booking, err := c.BookingRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Booking not found")
	}

	return ctx.JSON(http.StatusOK, booking)
}

func (c *BookingController) UpdateBooking(ctx echo.Context) error {
	id := ctx.Param("id")

	// Mengambil data booking yang sudah ada
	existingBooking, err := c.BookingRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Booking not found")
	}

	booking := new(models.Booking)
	if err := ctx.Bind(booking); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Menggunakan created_at yang sudah ada
	booking.CreatedAt = existingBooking.CreatedAt

	// Tetapkan booking code yang sudah ada pada data baru
	booking.BookingCode = existingBooking.BookingCode

	err = c.BookingRepository.Update(id, booking)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update booking")
	}

	return ctx.JSON(http.StatusOK, booking)
}

func (c *BookingController) UpdateBookingStatus(ctx echo.Context) error {
	id := ctx.Param("id")

	// Mengambil data booking yang sudah ada
	existingBooking, err := c.BookingRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Booking not found")
	}

	booking := new(models.Booking)
	if err := ctx.Bind(booking); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	// Update hanya status booking
	existingBooking.Status = booking.Status

	err = c.BookingRepository.UpdateStatus(id, existingBooking.Status)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update booking status")
	}

	return ctx.JSON(http.StatusOK, existingBooking)
}



func (c *BookingController) DeleteBooking(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.BookingRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete booking")
	}

	return ctx.NoContent(http.StatusNoContent)
}
