package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type OrderItem struct {
	ID        primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	OrderID   primitive.ObjectID `bson:"order_id" json:"order_id"`
	ProductID primitive.ObjectID `bson:"product_id" json:"product_id"`
	Quantity  int                `bson:"quantity" json:"quantity"`
	CreatedAt int64              `bson:"created_at" json:"created_at"`
}
