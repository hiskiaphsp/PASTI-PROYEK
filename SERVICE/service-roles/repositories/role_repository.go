package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-role/config"
	"github.com/hiskiaphsp/service-role/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type RoleRepository struct{}

func (r *RoleRepository) Create(role *models.Role) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), role)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *RoleRepository) GetAll() ([]*models.Role, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var roles []*models.Role
	err = cur.All(context.Background(), &roles)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return roles, nil
}

func (r *RoleRepository) GetByID(id string) (*models.Role, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	role := &models.Role{}
	err = collection.FindOne(context.TODO(), filter).Decode(role)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return role, nil
}

func (r *RoleRepository) Update(id string, role *models.Role) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": role}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *RoleRepository) Delete(id string) error {
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
