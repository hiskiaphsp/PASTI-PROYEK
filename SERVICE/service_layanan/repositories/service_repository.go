package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-service/config"
	"github.com/hiskiaphsp/service-service/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type ServiceRepository struct{}

func (r *ServiceRepository) Create(service *models.Service) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), service)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *ServiceRepository) GetAll() ([]*models.Service, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var services []*models.Service
	err = cur.All(context.Background(), &services)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return services, nil
}

func (r *ServiceRepository) GetByID(id string) (*models.Service, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	service := &models.Service{}
	err = collection.FindOne(context.TODO(), filter).Decode(service)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return service, nil
}

func (r *ServiceRepository) Update(id string, service *models.Service) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": service}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *ServiceRepository) Delete(id string) error {
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
