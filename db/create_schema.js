use todoApp;

db.createCollection("todos", {
    "storageEngine": {
        "wiredTiger": {}
    },
    "capped": false,
    "validator": {
        "$jsonSchema": {
            "bsonType": "object",
            "title": "todos",
            "properties": {
                "_id": {
                    "bsonType": "objectId"
                },
                "title": {
                    "bsonType": "string",
                    "maxLength": 255
                },
                "status": {
                    "bsonType": "string",
                    "enum": [
                        "Pending",
                        "In Process",
                        "Completed"
                    ]
                },
                "createdBy": {
                    "bsonType": [
                        "objectId",
                        "null"
                    ]
                },
                "createdAt": {
                    "bsonType": [
                        "date",
                        "string"
                    ]
                },
                "completedAt": {
                    "bsonType": [
                        "date",
                        "null"
                    ]
                }
            },
            "additionalProperties": false,
            "required": [
                "title",
                "status",
                "createdAt"
            ]
        }
    },
    "validationLevel": "strict",
    "validationAction": "error"
});




db.createCollection("users", {
    "storageEngine": {
        "wiredTiger": {}
    },
    "capped": false,
    "validator": {
        "$jsonSchema": {
            "bsonType": "object",
            "title": "users",
            "properties": {
                "_id": {
                    "bsonType": "objectId"
                },
                "username": {
                    "bsonType": "string",
                    "maxLength": 50,
                    "minLength": 3
                },
                "password": {
                    "bsonType": "string",
                    "minLength": 6,
                    "maxLength": 100
                },
                "email": {
                    "bsonType": "string",
                    "maxLength": 50
                },
                "registerDate": {
                    "bsonType": [
                        "date",
                        "string"
                    ]
                },
                "createdTodos": {
                    "bsonType": "number"
                },
                "avatarUrl": {
                    "bsonType": [
                        "string",
                        "null"
                    ],
                    "maxLength": 255
                }
            },
            "additionalProperties": false,
            "required": [
                "username",
                "password",
                "email",
                "registerDate",
                "createdTodos"
            ]
        }
    },
    "validationLevel": "strict",
    "validationAction": "error"
});