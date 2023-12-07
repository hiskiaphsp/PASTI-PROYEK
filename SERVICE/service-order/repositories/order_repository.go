package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-order/config"
	"github.com/hiskiaphsp/service-order/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type OrderRepository struct{}

func (r *OrderRepository) Create(order *models.Order) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), order)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *OrderRepository) GetAll() ([]*models.Order, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var orders []*models.Order
	err = cur.All(context.Background(), &orders)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return orders, nil
}

func (r *OrderRepository) GetByID(id string) (*models.Order, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	order := &models.Order{}
	err = collection.FindOne(context.TODO(), filter).Decode(order)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return order, nil
}

func (r *OrderRepository) Update(id string, order *models.Order) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": order}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *OrderRepository) UpdateStatus(id string, status string) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": bson.M{"order_status": status}}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *OrderRepository) Delete(id string) error {
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
