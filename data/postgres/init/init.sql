begin;

create schema if not exists mysite;

create extension if not exists "uuid-ossp";

set search_path to mysite, public;

set datestyle to 'ISO, DMY';

-- Updates timestamps on record update
create function trigger_set_timestamp() returns trigger as $$
  begin
    new.updated_at = now();
    return new;
  end;
$$ language plpgsql;

create table mysite.users (
  id uuid not null primary key default uuid_generate_v4(),
  first_name varchar(20) not null,
  last_name varchar(20) not null,
  email varchar(255) not null default '',
  created timestamptz not null default now(),
  updated timestamptz not null default now()
);

create unique index user_email on users (email);

create index user_created_at on users (created);
create index user_updated_at on users (updated);

create trigger update_user_timestamp
  before update on mysite.users
  for each row
    execute procedure trigger_set_timestamp();


insert into users (first_name, last_name, email)
values
    ('Fox', 'Mulder', 'fox@example.com'),
    ('Dana', 'Scully', 'dana@example.com'),
    ('Walter', 'Skinner', 'walter@example.com');

commit;

