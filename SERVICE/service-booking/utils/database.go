package utils

import (
	"context"
	"log"

	"github.com/hiskiaphsp/service-booking/config"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

func ConnectDB() (*mongo.Database, error) {
	clientOptions := options.Client().ApplyURI(config.MongoURI)

	client, err := mongo.Connect(context.TODO(), clientOptions)
	if err != nil {
		log.Fatal(err)
		return nil, err
	}

	err = client.Ping(context.TODO(), nil)
	if err != nil {
		log.Fatal(err)
		return nil, err
	}

	db := client.Database(config.DBName)

	return db, nil
}
