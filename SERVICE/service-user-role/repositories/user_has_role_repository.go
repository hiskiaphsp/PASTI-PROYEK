package repositories

import (
	"context"
	"fmt"
	"log"

	"github.com/hiskiaphsp/service-user-role/config"
	"github.com/hiskiaphsp/service-user-role/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
)

type UserRoleRepository struct{}

func (r *UserRoleRepository) Create(userRole *models.UserRole) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), userRole)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *UserRoleRepository) GetAll() ([]*models.UserRole, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var userRoles []*models.UserRole
	err = cur.All(context.Background(), &userRoles)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return userRoles, nil
}

func (r *UserRoleRepository) GetByID(id string) (*models.UserRole, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	userRole := &models.UserRole{}
	err = collection.FindOne(context.TODO(), filter).Decode(userRole)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return userRole, nil
}

func (r *UserRoleRepository) GetByUserID(userID string) (*models.UserRole, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	// Mengubah userID menjadi tipe primitive.ObjectID
	objectID, err := primitive.ObjectIDFromHex(userID)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	filter := bson.M{"user_id": objectID}
	userRole := &models.UserRole{}
	err = collection.FindOne(context.TODO(), filter).Decode(userRole)
	if err != nil {
		if err == mongo.ErrNoDocuments {
			return nil, fmt.Errorf("User role not found")
		}
		log.Println(err)
		return nil, err
	}

	return userRole, nil
}


func (r *UserRoleRepository) Update(id string, userRole *models.UserRole) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": userRole}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *UserRoleRepository) Delete(id string) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	_, err = collection.DeleteOne(context.TODO(), filter)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}
