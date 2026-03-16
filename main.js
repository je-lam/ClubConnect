/* Current time line on the calendar page */

function updateTimeLine() {
  var line = document.getElementById("current-time-line");
  if (!line) return;

  var now = new Date();
  var hours = now.getHours();
  var minutes = now.getMinutes();
  var startHour = 8;
  var endHour = 20;

  var minutesFromStart = (hours - startHour) * 60 + minutes;

  if (minutesFromStart < 0 || minutesFromStart > (endHour - startHour) * 60) {
    line.style.display = "none";
    return;
  }

  line.style.top = minutesFromStart + "px";
  line.style.display = "block";
}

updateTimeLine();
setInterval(updateTimeLine, 60000);

/* Tag filtering on the directory page */

var tagPills = document.querySelectorAll(".tag-pill");
var clubCards = document.querySelectorAll(".club-card");
var categorySections = document.querySelectorAll(".category-section");

tagPills.forEach(function (pill) {
  pill.addEventListener("click", function () {
    var selectedTag = pill.dataset.tag;
    var isAlreadyActive = pill.classList.contains("active");

    tagPills.forEach(function (p) {
      p.classList.remove("active");
    });

    if (isAlreadyActive) {
      // clear the filter, show everything
      clubCards.forEach(function (card) {
        card.style.display = "";
      });
      categorySections.forEach(function (section) {
        section.style.display = "";
      });
    } else {
      pill.classList.add("active");

      clubCards.forEach(function (card) {
        var cardTags = card.dataset.tags.split(",");
        card.style.display = cardTags.includes(selectedTag) ? "" : "none";
      });

      // hide any category section where all clubs are now hidden
      categorySections.forEach(function (section) {
        var visibleCards = section.querySelectorAll(".club-card[style=''],.club-card:not([style])");
        section.style.display = visibleCards.length === 0 ? "none" : "";
      });
    }
  });
});

/* Grid and list view toggle on the directory page */

var toggleBtn = document.getElementById("toggle-view-btn");
var allGrids = document.querySelectorAll(".club-grid, .club-list");
var isGridView = true;

if (toggleBtn) {
  toggleBtn.addEventListener("click", function () {
    if (isGridView) {
      allGrids.forEach(function (grid) {
        grid.classList.remove("club-grid");
        grid.classList.add("club-list");
      });
      toggleBtn.textContent = "Switch to Grid View";
      isGridView = false;
    } else {
      allGrids.forEach(function (grid) {
        grid.classList.remove("club-list");
        grid.classList.add("club-grid");
      });
      toggleBtn.textContent = "Switch to List View";
      isGridView = true;
    }
  });
}

/* Recently viewed clubs using localStorage */

function getRecentlyViewed() {
  var stored = localStorage.getItem("recentlyViewed");
  if (stored) {
    return JSON.parse(stored);
  }
  return [];
}

function saveRecentlyViewed(id, name) {
  var recent = getRecentlyViewed();

  // remove this club if it's already in the list so we don't get duplicates
  recent = recent.filter(function (item) {
    return item.id !== id;
  });

  // add to the front of the list
  recent.unshift({ id: id, name: name });

  // keep it to 5 items max
  if (recent.length > 5) {
    recent = recent.slice(0, 5);
  }

  localStorage.setItem("recentlyViewed", JSON.stringify(recent));
}

function showRecentlyViewed() {
  var list = document.getElementById("recently-viewed-list");
  if (!list) return;

  var recent = getRecentlyViewed();

  if (recent.length === 0) {
    list.innerHTML = '<p class="no-recent">Nothing viewed yet.</p>';
    return;
  }

  list.innerHTML = "";

  recent.forEach(function (item) {
    var link = document.createElement("a");
    link.href = "/club.php?id=" + item.id;
    link.className = "recent-club-item";

    var avatar = document.createElement("div");
    avatar.className = "recent-club-avatar";
    avatar.textContent = item.name.charAt(0);

    var label = document.createElement("p");
    label.textContent = item.name;

    link.appendChild(avatar);
    link.appendChild(label);
    list.appendChild(link);
  });
}

/* Save to recently viewed when on the club detail page */

var clubDetailName = document.querySelector(".club-detail-title");

if (clubDetailName) {
  // reads the ?id=123 value out of the page url
  var clubId = new URLSearchParams(window.location.search).get("id");
  var clubName = clubDetailName.textContent.trim();

  if (clubId && clubName) {
    saveRecentlyViewed(clubId, clubName);
  }
}

/* Show recently viewed when on the directory page */

showRecentlyViewed();
