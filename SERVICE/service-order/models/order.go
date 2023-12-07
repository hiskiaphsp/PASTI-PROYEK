package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Order struct {
	ID           primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	UserID       primitive.ObjectID `bson:"user_id" json:"user_id"`
	OrderAmount  float64            `bson:"order_amount" json:"order_amount"`
	OrderStatus  string             `bson:"order_status" json:"order_status"`
	PaymentMethod string             `bson:"payment_method" json:"payment_method"`
	CreatedAt    int64              `bson:"created_at" json:"created_at"`
}
