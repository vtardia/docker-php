let error = true

let res = [
  db.dummies.drop(),
  db.dummies.drop(),
  db.dummies.createIndex({ myfield: 1 }, { unique: true }),
  db.dummies.createIndex({ thatfield: 1 }),
  db.dummies.createIndex({ thatfield: 1 }),
  db.dummies.insert({ myfield: 'hello', thatfield: 'testing' }),
  db.dummies.insert({ myfield: 'hello2', thatfield: 'testing' }),
  db.dummies.insert({ myfield: 'hello3', thatfield: 'testing' }),
]

printjson(res)

if (error) {
  print('Error, exiting')
  quit(1)
}
