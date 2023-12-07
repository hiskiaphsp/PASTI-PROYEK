package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type UserRole struct {
	ID      primitive.ObjectID `bson:"_id,omitempty" json:"id"`
	RoleID  primitive.ObjectID `bson:"role_id" json:"role_id"`
	UserID  primitive.ObjectID `bson:"user_id" json:"user_id"`
}
