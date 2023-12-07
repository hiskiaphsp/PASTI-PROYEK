package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Service struct {
	ID          primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	ServiceName string             `bson:"service_name" json:"service_name"`
	ServiceImage string            `bson:"service_image" json:"service_image"`
	Description string             `bson:"description" json:"description"`
	Price       float64            `bson:"price" json:"price"`
	CreatedAt   int64              `bson:"created_at" json:"created_at"`
}
