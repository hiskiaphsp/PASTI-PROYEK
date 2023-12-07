package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Product struct {
	ID          primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	Name        string             `bson:"product_name" json:"product_name"`
	SKU         string             `bson:"sku" json:"sku"`
	Image       string             `bson:"product_image" json:"product_image"`
	Description string             `bson:"product_description" json:"product_description"`
	Price       float64            `bson:"product_price" json:"product_price"`
	Stock       int                `bson:"product_stock" json:"product_stock"`
	CreatedAt   int64              `bson:"created_at" json:"created_at"`
}
