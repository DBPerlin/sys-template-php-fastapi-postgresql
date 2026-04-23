import os
import pymysql


def conectar():
    return pymysql.connect(
        host=os.environ["CNN_DB_HOST"],
        port=int(os.environ.get("CNN_DB_PORT", 3306)),
        user=os.environ["CNN_DB_USER"],
        password=os.environ["CNN_DB_PASSWORD"],
        database=os.environ.get("CNN_DB_NAME", "db_list_nutricionistas"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=False,
    )
