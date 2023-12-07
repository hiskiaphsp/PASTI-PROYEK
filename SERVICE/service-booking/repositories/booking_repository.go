package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-booking/config"
	"github.com/hiskiaphsp/service-booking/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type BookingRepository struct{}

func (r *BookingRepository) Create(booking *models.Booking) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), booking)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *BookingRepository) GetAll() ([]*models.Booking, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var bookings []*models.Booking
	err = cur.All(context.Background(), &bookings)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return bookings, nil
}

func (r *BookingRepository) GetByID(id string) (*models.Booking, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	booking := &models.Booking{}
	err = collection.FindOne(context.TODO(), filter).Decode(booking)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return booking, nil
}

func (r *BookingRepository) Update(id string, booking *models.Booking) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": booking}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *BookingRepository) UpdateStatus(id string, status string) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": bson.M{"status": status}}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}



func (r *BookingRepository) Delete(id string) error {
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
