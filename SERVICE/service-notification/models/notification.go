package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type Notification struct {
	ID      primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	UserID  primitive.ObjectID `bson:"user_id" json:"user_id"`
	Message string             `bson:"message" json:"message"`
	Type    string             `bson:"type" json:"type"`
	Read    bool               `bson:"read" json:"read"`
}
