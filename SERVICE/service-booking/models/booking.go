package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Booking struct {
	ID                primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	ServiceID         primitive.ObjectID `bson:"service_id" json:"service_id"`
	UserID            primitive.ObjectID `bson:"user_id" json:"user_id"`
	PhoneNumber       string             `bson:"phone_number" json:"phone_number"`
	StartBookingDate  int64              `bson:"start_booking_date" json:"start_booking_date"`
	EndBookingDate    int64              `bson:"end_booking_date" json:"end_booking_date"`
	PaymentMethod     string             `bson:"payment_method" json:"payment_method"`
	Status            string             `bson:"status" json:"status"`
	BookingCode       string             `bson:"booking_code" json:"booking_code"`
	BookingDescription string             `bson:"booking_description" json:"booking_description"`
	CreatedAt         int64              `bson:"created_at" json:"created_at"`
}
