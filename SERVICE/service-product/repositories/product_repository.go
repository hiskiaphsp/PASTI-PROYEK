package repositories

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-product/config"
	"github.com/hiskiaphsp/service-product/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
)

type ProductRepository struct{}

func (r *ProductRepository) Create(product *models.Product) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), product)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *ProductRepository) GetAll() ([]*models.Product, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var products []*models.Product
	err = cur.All(context.Background(), &products)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return products, nil
}

func (r *ProductRepository) GetByID(id string) (*models.Product, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	product := &models.Product{}
	err = collection.FindOne(context.TODO(), filter).Decode(product)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return product, nil
}

func (r *ProductRepository) Update(id string, product *models.Product) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": product}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *ProductRepository) Delete(id string) error {
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
