package main

import (
	"log"
	"os"

	"github.com/hiskiaphsp/service-order-item/routes"
	"github.com/joho/godotenv"
)

func init() {
	err := godotenv.Load()
	if err != nil {
		log.Fatal("Error loading .env file")
	}
}

func main() {
	port := os.Getenv("PORT")
	if port == "" {
		port = "8080" // Set default port if not provided in .env
	}

	app := routes.InitRoutes()

	err := app.Start(":" + port)
	if err != nil {
		log.Fatal(err)
	}
}
