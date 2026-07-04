-- Sartu PHP/MySQL Schema
-- Ausführen in der MySQL-Datenbank der eigenen Server-Installation.

create table if not exists profiles (
  id          char(36) primary key,
  created_at  datetime not null default current_timestamp,
  email       varchar(190) not null unique,
  name        varchar(190) null,
  firma       varchar(190) null,
  telefon     varchar(80) null,
  role        enum('customer','admin') not null default 'customer',
  is_active   tinyint(1) not null default 1,
  index idx_profiles_role (role)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists login_tokens (
  id          char(36) primary key,
  profile_id  char(36) not null,
  email       varchar(190) not null,
  code_hash   char(64) not null,
  link_hash   char(64) not null,
  created_at  datetime not null default current_timestamp,
  expires_at  datetime not null,
  consumed_at datetime null,
  index idx_login_email (email),
  index idx_login_link_hash (link_hash),
  constraint fk_login_profile foreign key (profile_id) references profiles(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists briefings (
  id            char(36) primary key,
  created_at    datetime not null default current_timestamp,
  updated_at    datetime not null default current_timestamp on update current_timestamp,
  payload       json null,
  status        enum('neu','in_bearbeitung','umgewandelt','abgelehnt') not null default 'neu',
  kontakt_email varchar(190) null,
  kontakt_name  varchar(190) null,
  index idx_briefings_status (status),
  index idx_briefings_created_at (created_at)
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists projects (
  id            char(36) primary key,
  created_at    datetime not null default current_timestamp,
  updated_at    datetime not null default current_timestamp on update current_timestamp,
  customer_id   char(36) not null,
  titel         varchar(255) null,
  paket         varchar(80) null,
  care_stufe    varchar(80) null,
  phase         enum('angebot_bestaetigt','inhalte_liefern','design_laeuft','korrektur_1','korrektur_2','korrektur_3','korrektur_4','finalisierung','live') not null default 'angebot_bestaetigt',
  notiz_kunde   text null,
  notiz_intern  text null,
  liefertermin  date null,
  index idx_projects_customer (customer_id),
  index idx_projects_phase (phase),
  constraint fk_projects_customer foreign key (customer_id) references profiles(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists uploads (
  id              char(36) primary key,
  created_at      datetime not null default current_timestamp,
  project_id      char(36) not null,
  typ             varchar(80) null,
  storage_path    varchar(500) null,
  original_name   varchar(255) null,
  hochgeladen_von char(36) null,
  constraint fk_uploads_project foreign key (project_id) references projects(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists feedback_rounds (
  id              char(36) primary key,
  created_at      datetime not null default current_timestamp,
  project_id      char(36) not null,
  runde           int null,
  inhalt          text null,
  eingereicht_am  datetime null,
  constraint fk_feedback_project foreign key (project_id) references projects(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists care_entries (
  id            char(36) primary key,
  created_at    datetime not null default current_timestamp,
  customer_id   char(36) not null,
  datum         date null,
  beschreibung  text null,
  minuten       int null,
  constraint fk_care_customer foreign key (customer_id) references profiles(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists documents (
  id            char(36) primary key,
  created_at    datetime not null default current_timestamp,
  customer_id   char(36) not null,
  project_id    char(36) null,
  typ           varchar(80) null,
  storage_path  varchar(500) null,
  titel         varchar(255) null,
  constraint fk_documents_customer foreign key (customer_id) references profiles(id) on delete cascade,
  constraint fk_documents_project foreign key (project_id) references projects(id) on delete set null
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

-- Ersten Admin anlegen: E-Mail anpassen, danach über /login einloggen.
-- insert into profiles (id, email, name, role)
-- values (uuid(), 'admin@deine-domain.de', 'Sartu Admin', 'admin');
