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
  alt_text        varchar(500) null,
  mime            varchar(120) null,
  bytes           int null,
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

-- ===== Selbst-Editor / Inhalts-Modell (Stufe 1) =====
-- Eine Kundenseite (site_pages) besteht aus Feldern (site_blocks). Jedes Feld hat
-- einen Entwurfs- und einen Veröffentlicht-Wert. "Veröffentlichen" kopiert Entwurf →
-- Live. Vor jeder Veröffentlichung wird ein Schnappschuss (site_page_versions) für
-- "Rückgängig" abgelegt. Welche Sektionen/Felder eine Seite hat, definiert das
-- Feld-Schema in includes/site-content-schema.php (Spalte vorlage).

create table if not exists site_pages (
  id           char(36) primary key,
  created_at   datetime not null default current_timestamp,
  updated_at   datetime not null default current_timestamp on update current_timestamp,
  project_id   char(36) not null,
  slug         varchar(120) not null,
  vorlage      varchar(80) not null default 'standard',
  titel        varchar(255) null,
  nav_label    varchar(120) null,
  typ          varchar(20) not null default 'inhalt',
  aktiv        tinyint(1) not null default 1,
  position     int not null default 0,
  is_published tinyint(1) not null default 0,
  index idx_site_pages_project (project_id),
  unique key uq_site_pages_slug (project_id, slug),
  constraint fk_site_pages_project foreign key (project_id) references projects(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists site_blocks (
  id             char(36) primary key,
  created_at     datetime not null default current_timestamp,
  updated_at     datetime not null default current_timestamp on update current_timestamp,
  page_id        char(36) not null,
  section_key    varchar(120) not null,
  field_key      varchar(120) not null,
  wert_draft     longtext null,
  wert_published longtext null,
  unique key uq_site_blocks_field (page_id, section_key, field_key),
  index idx_site_blocks_page (page_id),
  constraint fk_site_blocks_page foreign key (page_id) references site_pages(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table if not exists site_page_versions (
  id           char(36) primary key,
  created_at   datetime not null default current_timestamp,
  page_id      char(36) not null,
  anlass       varchar(80) null,
  snapshot     longtext null,
  erstellt_von char(36) null,
  index idx_site_page_versions_page (page_id),
  constraint fk_spv_page foreign key (page_id) references site_pages(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

-- ===== Auftragsmechanismus: Angebot -> verbindliche Zusage =====
-- Admin erstellt ein Angebot (aus einer Anfrage), der Kunde nimmt es im Portal
-- verbindlich an (AGB + Protokoll). Bei Annahme wird das Projekt erstellt (project_id)
-- und ein unveraenderbarer Schnappschuss (snapshot) festgehalten.
create table if not exists angebote (
  id             char(36) primary key,
  created_at     datetime not null default current_timestamp,
  updated_at     datetime not null default current_timestamp on update current_timestamp,
  briefing_id    char(36) null,
  customer_id    char(36) not null,
  titel          varchar(255) null,
  paket          varchar(80) null,
  preis_einmalig int null,
  care_stufe     varchar(80) null,
  care_preis     int null,
  korrekturrunden int null,
  umfang         text null,
  liefertext     varchar(255) null,
  hinweis        text null,
  gueltig_bis    date null,
  status         enum('entwurf','gesendet','angenommen','abgelehnt') not null default 'entwurf',
  angenommen_am  datetime null,
  angenommen_ip  varchar(64) null,
  agb_version    varchar(40) null,
  snapshot       json null,
  project_id     char(36) null,
  index idx_angebote_customer (customer_id),
  index idx_angebote_status (status),
  constraint fk_angebote_customer foreign key (customer_id) references profiles(id) on delete cascade,
  constraint fk_angebote_briefing foreign key (briefing_id) references briefings(id) on delete set null,
  constraint fk_angebote_project foreign key (project_id) references projects(id) on delete set null
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

-- ===== Stufe-2-Briefing (Detail-Onboarding im Portal) =====
-- Ein Briefing je Projekt. answers = JSON der Antworten (Felder-Keys), Datei-Uploads
-- referenzieren uploads.id. status offen|abgeschlossen.
create table if not exists project_briefings (
  id           char(36) primary key,
  created_at   datetime not null default current_timestamp,
  updated_at   datetime not null default current_timestamp on update current_timestamp,
  project_id   char(36) not null,
  answers      json null,
  status       enum('offen','abgeschlossen') not null default 'offen',
  submitted_at datetime null,
  unique key uq_project_briefings (project_id),
  constraint fk_project_briefings_project foreign key (project_id) references projects(id) on delete cascade
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

-- Ersten Admin anlegen: E-Mail anpassen, danach über /login einloggen.
-- insert into profiles (id, email, name, role)
-- values (uuid(), 'admin@deine-domain.de', 'Sartu Admin', 'admin');
