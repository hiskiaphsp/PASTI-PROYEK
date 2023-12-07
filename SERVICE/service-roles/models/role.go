package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Role struct {
	ID        primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	Name      string             `bson:"name" json:"name"`
	CreatedAt int64              `bson:"created_at" json:"created_at"`
}
