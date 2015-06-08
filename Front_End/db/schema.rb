# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 1) do

  create_table "authors", id: false, force: :cascade do |t|
    t.string  "docID",     limit: 32
    t.integer "rank",      limit: 4
    t.string  "LastName",  limit: 64
    t.string  "FirstName", limit: 64
  end

  create_table "docs", id: false, force: :cascade do |t|
    t.string "docID",       limit: 32
    t.string "dateStamp",   limit: 12
    t.string "dateCreated", limit: 12
    t.string "dateUpdated", limit: 12
    t.string "categories",  limit: 64
    t.text   "title",       limit: 65535
    t.text   "comments",    limit: 65535
    t.text   "abstract",    limit: 65535
    t.string "reportNo",    limit: 255
    t.string "doi",         limit: 255
    t.text   "journalRef",  limit: 65535
  end

  add_index "docs", ["docID"], name: "docID", unique: true, using: :btree

  create_table "files", id: false, force: :cascade do |t|
    t.string "docID", limit: 255
    t.text   "pdf",   limit: 65535
    t.text   "src",   limit: 65535
  end

  create_table "setSpecs", id: false, force: :cascade do |t|
    t.string "docID",   limit: 32
    t.string "setSpec", limit: 64
  end

end
