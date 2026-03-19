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

/* Tag filtering and search on the directory page */

var tagPills = document.querySelectorAll(".tag-pill");
var clubCards = document.querySelectorAll(".club-card");
var categorySections = document.querySelectorAll(".category-section");
var searchInput = document.getElementById("search-input");
var directorySearchForm = document.getElementById("directory-search-form");

function clubMatchesSearch(card, query) {
  if (!query) return true;
  var name = (card.dataset.name || "").toLowerCase();
  var tags = (card.dataset.tags || "").toLowerCase().split(",").map(function (t) {
    return t.trim();
  }).filter(Boolean);
  var section = card.closest(".category-section");
  var category = (section && section.dataset.category) ? section.dataset.category.toLowerCase() : "";
  return name.indexOf(query) !== -1 ||
    tags.some(function (t) {
      return t.indexOf(query) !== -1;
    }) ||
    category.indexOf(query) !== -1;
}

function clubMatchesTag(card, selectedTag) {
  if (!selectedTag) return true;
  var cardTags = (card.dataset.tags || "").split(",").map(function (t) {
    return t.trim();
  }).filter(Boolean);
  return cardTags.indexOf(selectedTag) !== -1;
}

function applyFilters() {
  var query = (searchInput && searchInput.value) ? searchInput.value.trim().toLowerCase() : "";
  var activePill = document.querySelector(".tag-pill.active");
  var selectedTag = activePill ? activePill.dataset.tag : null;

  clubCards.forEach(function (card) {
    var matchesSearch = clubMatchesSearch(card, query);
    var matchesTag = clubMatchesTag(card, selectedTag);
    card.style.display = matchesSearch && matchesTag ? "" : "none";
  });

  categorySections.forEach(function (section) {
    var visibleCards = section.querySelectorAll(".club-card[style=''],.club-card:not([style])");
    section.style.display = visibleCards.length === 0 ? "none" : "";
  });
}

if (directorySearchForm) {
  directorySearchForm.addEventListener("submit", function (event) {
    event.preventDefault();
    applyFilters();
  });
}

if (searchInput) {
  searchInput.addEventListener("input", function () {
    applyFilters();
  });
}

tagPills.forEach(function (pill) {
  pill.addEventListener("click", function () {
    var selectedTag = pill.dataset.tag;
    var isAlreadyActive = pill.classList.contains("active");

    tagPills.forEach(function (p) {
      p.classList.remove("active");
    });

    if (!isAlreadyActive) {
      pill.classList.add("active");
    }

    applyFilters();
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

function saveRecentlyViewed(id, name, logoUrl) {
  var recent = getRecentlyViewed();

  // remove this club if it's already in the list so we don't get duplicates
  recent = recent.filter(function (item) {
    return item.id !== id;
  });

  // add to the front of the list
  recent.unshift({
    id: id,
    name: name,
    logoUrl: logoUrl || "",
  });

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

  var needsResave = false;

  recent.forEach(function (item) {
    // If we dont have a logoUrl saved yet, try to read it from the corresponding club card on the directory page
    if (!item.logoUrl) {
      var card = document.querySelector('.club-card[data-id="' + item.id + '"]');
      if (card) {
        var cardLogoImg = card.querySelector(".club-card-logo");
        if (cardLogoImg && cardLogoImg.getAttribute("src")) {
          item.logoUrl = cardLogoImg.getAttribute("src");
          needsResave = true;
        }
      }
    }

    var link = document.createElement("a");
    link.href = "/club.php?id=" + item.id;
    link.className = "recent-club-item";

    var avatar = document.createElement("div");
    avatar.className = "recent-club-avatar";

    if (item.logoUrl) {
      var img = document.createElement("img");
      img.src = item.logoUrl;
      img.alt = "";
      avatar.appendChild(img);
    } else {
      avatar.textContent = item.name.charAt(0);
    }

    var label = document.createElement("p");
    label.textContent = item.name;

    link.appendChild(avatar);
    link.appendChild(label);
    list.appendChild(link);
  });

  // If we discovered any missing logo URLs, persist the updated list
  // so future loads don't need to re-derive them.
  if (needsResave) {
    localStorage.setItem("recentlyViewed", JSON.stringify(recent));
  }
}

/* Save to recently viewed when on the club detail page */

var clubDetailName = document.querySelector(".club-detail-title");

if (clubDetailName) {
  // reads the ?id=123 value out of the page url
  var clubId = new URLSearchParams(window.location.search).get("id");
  var clubName = clubDetailName.textContent.trim();

  if (clubId && clubName) {
    var clubLogoImg = document.querySelector(".club-detail-logo");
    var clubLogoUrl = clubLogoImg ? clubLogoImg.getAttribute("src") : "";

    saveRecentlyViewed(clubId, clubName, clubLogoUrl);
  }
}

showRecentlyViewed();
