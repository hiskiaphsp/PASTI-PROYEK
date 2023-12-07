package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-order-item/config"
	"github.com/hiskiaphsp/service-order-item/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type OrderItemRepository struct{
	
}

func (r *OrderItemRepository) Create(orderItem *models.OrderItem) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), orderItem)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *OrderItemRepository) GetAll() ([]*models.OrderItem, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var orderItems []*models.OrderItem
	err = cur.All(context.Background(), &orderItems)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return orderItems, nil
}
func (r *OrderItemRepository) GetByOrderID(orderID primitive.ObjectID) ([]*models.OrderItem, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"order_id": orderID}
	cur, err := collection.Find(context.TODO(), filter)
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.TODO())

	orderItems := []*models.OrderItem{}
	for cur.Next(context.TODO()) {
		orderItem := &models.OrderItem{}
		err := cur.Decode(orderItem)
		if err != nil {
			log.Println(err)
			return nil, err
		}
		orderItems = append(orderItems, orderItem)
	}

	if err := cur.Err(); err != nil {
		log.Println(err)
		return nil, err
	}

	return orderItems, nil
}
func (r *OrderItemRepository) GetByID(id string) (*models.OrderItem, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	orderItem := &models.OrderItem{}
	err = collection.FindOne(context.TODO(), filter).Decode(orderItem)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return orderItem, nil
}

func (r *OrderItemRepository) Update(id string, orderItem *models.OrderItem) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": orderItem}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *OrderItemRepository) Delete(id string) error {
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
