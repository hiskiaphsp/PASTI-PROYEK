package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-notification/config"
	"github.com/hiskiaphsp/service-notification/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type NotificationRepository struct{}

func (r *NotificationRepository) Create(notification *models.Notification) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), notification)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *NotificationRepository) GetAll() ([]*models.Notification, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var notifications []*models.Notification
	err = cur.All(context.Background(), &notifications)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return notifications, nil
}

func (r *NotificationRepository) GetByID(id string) (*models.Notification, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	notification := &models.Notification{}
	err = collection.FindOne(context.TODO(), filter).Decode(notification)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return notification, nil
}

func (r *NotificationRepository) Update(id string, notification *models.Notification) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": notification}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *NotificationRepository) Delete(id string) error {
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
