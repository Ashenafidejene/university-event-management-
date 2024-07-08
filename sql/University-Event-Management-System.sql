CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) UNIQUE,
    FullName VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    Password VARCHAR(100),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Events (
    EventID INT PRIMARY KEY AUTO_INCREMENT,
    EventName VARCHAR(255),
    EventDescription TEXT,
    EventDateTime DATETIME,
    Location VARCHAR(255),
    Category VARCHAR(50),
    Status ENUM('draft', 'published', 'cancelled') DEFAULT 'draft',
    WriterUsername VARCHAR(50),
    ViewCount INT DEFAULT 0,
    AttendeeCount INT DEFAULT 0,
    LikeCount INT DEFAULT 0,
    DislikeCount INT DEFAULT 0,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (WriterUsername) REFERENCES Admin(Username)
);

CREATE TABLE EventComment (
    CommentID INT PRIMARY KEY AUTO_INCREMENT,
    EventID INT,
    Comment TEXT,
    ParentCommentID INT,
    UserUsername VARCHAR(50),
    ReportedCount INT DEFAULT 0,
    CommentDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (EventID) REFERENCES Event(EventID),
    FOREIGN KEY (UserUsername) REFERENCES User(Username)
);

CREATE TABLE AdminReply (
    ReplyID INT PRIMARY KEY AUTO_INCREMENT,
    CommentID INT,
    Reply TEXT,
    AdminUsername VARCHAR(50),
    ReplyDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CommentID) REFERENCES EventComment(CommentID),
    FOREIGN KEY (AdminUsername) REFERENCES Admin(Username)
);

CREATE TABLE UserLike (
    LikeID INT PRIMARY KEY AUTO_INCREMENT,
    EventID INT,
    UserUsername VARCHAR(50),
    LikedDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (EventID) REFERENCES Event(EventID),
    FOREIGN KEY (UserUsername) REFERENCES User(Username),
    UNIQUE (EventID, UserUsername)
);

CREATE TABLE UserDislike (
    DislikeID INT PRIMARY KEY AUTO_INCREMENT,
    EventID INT,
    UserUsername VARCHAR(50),
    DislikedDateTime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (EventID) REFERENCES Event(EventID),
    FOREIGN KEY (UserUsername) REFERENCES User(Username),
    UNIQUE (EventID, UserUsername)
);

CREATE TABLE Notification (
    NotificationID INT PRIMARY KEY AUTO_INCREMENT,
    UserUsername VARCHAR(50),
    Message TEXT,
    Type ENUM('new_comment', 'event_update', 'admin_message'),
    IsSeen BOOLEAN DEFAULT FALSE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserUsername) REFERENCES User(Username)
);

CREATE TABLE Admin (
    Username VARCHAR(50) PRIMARY KEY,
    FullName VARCHAR(100),
    Email VARCHAR(100),
    PhoneNumber VARCHAR(20)
);

CREATE TABLE EventMetrics (
    MetricsID INT PRIMARY KEY AUTO_INCREMENT,
    EventID INT,
    TotalViews INT DEFAULT 0,
    TotalAttendees INT DEFAULT 0,
    TotalLikes INT DEFAULT 0,
    TotalDislikes INT DEFAULT 0,
    TotalComments INT DEFAULT 0,
    MetricsDate DATE,
    FOREIGN KEY (EventID) REFERENCES Event(EventID)
);
CREATE TABLE eventbooking (
    BookingID INT AUTO_INCREMENT PRIMARY KEY,
    EventID INT NOT NULL,
    UserID INT NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    BookingDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (EventID) REFERENCES events(EventID),
    FOREIGN KEY (UserID) REFERENCES users(UserID)
);