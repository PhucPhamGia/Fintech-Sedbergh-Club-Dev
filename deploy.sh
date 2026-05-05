#!/bin/bash

HOST="giaphuc_dev@103.77.160.104"
PORT="2229"
REMOTE="/home/thuytrieu.vn/giaphuc"

echo "Deploying to $HOST..."

scp -r -P $PORT public/           $HOST:$REMOTE/public/
scp -r -P $PORT app/Controllers/  $HOST:$REMOTE/app/Controllers/
scp -r -P $PORT app/Views/        $HOST:$REMOTE/app/Views/
scp -r -P $PORT app/Models/       $HOST:$REMOTE/app/Models/

if [ "$1" == "--config" ]; then
    echo "Deploying Config..."
    scp -r -P $PORT app/Config/ $HOST:$REMOTE/app/Config/
fi

echo "Done."
