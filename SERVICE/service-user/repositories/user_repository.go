package repositories

import (
	"context"
	"errors"
	"log"

	"github.com/hiskiaphsp/service-user/config"
	"github.com/hiskiaphsp/service-user/models"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
)
var (
	ErrUserNotFound = errors.New("user not found")
)

type UserRepository struct{}

func (r *UserRepository) Create(user *models.User) error {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	_, err := collection.InsertOne(context.TODO(), user)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (r *UserRepository) GetByEmail(email string) (*models.User, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"email": email}
	user := &models.User{}
	err := collection.FindOne(context.TODO(), filter).Decode(user)
	if err != nil {
		if err == mongo.ErrNoDocuments {
			return nil, ErrUserNotFound
		}
		log.Println(err)
		return nil, err
	}

	return user, nil
}

func (r *UserRepository) GetByID(id string) (*models.User, error) {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	user := &models.User{}
	err = collection.FindOne(context.TODO(), filter).Decode(user)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return user, nil
}
func (r *UserRepository) GetAll() ([]*models.User, error) {
	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	cur, err := collection.Find(context.TODO(), bson.M{})
	if err != nil {
		log.Println(err)
		return nil, err
	}
	defer cur.Close(context.Background())

	var users []*models.User
	err = cur.All(context.Background(), &users)
	if err != nil {
		log.Println(err)
		return nil, err
	}

	return users, nil
}



func (r *UserRepository) Update(id string, user *models.User) error {
	objectID, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Println(err)
		return err
	}

	collection := config.MongoDB.Database(config.DBName).Collection(config.Collection)
	filter := bson.M{"_id": objectID}
	update := bson.M{"$set": user}
	_, err = collection.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Println(err)
		return err
	}

	return nil
}

func (r *UserRepository) Delete(id string) error {
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
