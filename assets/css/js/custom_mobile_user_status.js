// Mobile User Status Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Parse data from hidden containers
    const mobileUserIdsContainer = document.getElementById('mobileUserIdsData');
    const allUsersContainer = document.getElementById('allUsersData');
    
    let mobileUserIds = [];
    let allUsersData = [];
    
    try {
        if (mobileUserIdsContainer && mobileUserIdsContainer.dataset.mobileIds) {
            // Decode HTML entities from attribute
            const mobileIdsStr = document.querySelector('[data-mobile-ids]').getAttribute('data-mobile-ids');
            mobileUserIds = JSON.parse(mobileIdsStr);
        }
        if (allUsersContainer && allUsersContainer.dataset.allUsers) {
            // Decode HTML entities from attribute
            const allUsersStr = document.querySelector('[data-all-users]').getAttribute('data-all-users');
            allUsersData = JSON.parse(allUsersStr);
        }
    } catch (e) {
        console.error('Error parsing data:', e);
    }

    // Event delegation: Listen for card clicks
    document.addEventListener('click', function(e) {
        const cardElement = e.target.closest('[data-action]');
        
        if (!cardElement) return;
        
        const action = cardElement.dataset.action;
        const cardType = cardElement.dataset.cardType;
        
        if (action === 'show-all-users') {
            handleShowAllUsers(cardElement, allUsersData, mobileUserIds);
        } else if (action === 'show-mobile-users') {
            handleShowMobileUsers(cardElement, allUsersData, mobileUserIds);
        } else if (action === 'show-needs-mobile') {
            handleShowNeedsMobile(cardElement, allUsersData, mobileUserIds);
        }
    });

    /**
     * Show all users in table
     */
    function handleShowAllUsers(cardElement, allUsers, mobileIds) {
        updateCardActive(cardElement);
        renderTable('All Users', allUsers, mobileIds, 'all');
    }

    /**
     * Show only users who have logged in mobile
     */
    function handleShowMobileUsers(cardElement, allUsers, mobileIds) {
        updateCardActive(cardElement);
        
        const filteredUsers = {};
        let index = 1;
        
        for (let key in allUsers) {
            const user = allUsers[key];
            if (mobileIds.includes(parseInt(user.id))) {
                filteredUsers[index] = user;
                index++;
            }
        }
        
        renderTable('Mobile Logged-In Users', filteredUsers, mobileIds, 'mobile');
    }

    /**
     * Show users who haven't logged in mobile
     */
    function handleShowNeedsMobile(cardElement, allUsers, mobileIds) {
        updateCardActive(cardElement);
        
        const filteredUsers = {};
        let index = 1;
        
        for (let key in allUsers) {
            const user = allUsers[key];
            if (!mobileIds.includes(parseInt(user.id))) {
                filteredUsers[index] = user;
                index++;
            }
        }
        
        renderTable('Need to Login Mobile', filteredUsers, mobileIds, 'needs-mobile');
    }

    /**
     * Update active card styling
     */
    function updateCardActive(activeCard) {
        // Remove active class from all cards
        document.querySelectorAll('.stats-card').forEach(card => {
            card.classList.remove('active');
        });
        
        // Add active class to clicked card
        activeCard.classList.add('active');
    }

    /**
     * Render user table
     */
    function renderTable(title, users, mobileIds, tableType) {
        const tableContainer = document.getElementById('userDetailsTable');
        const tableTitle = document.getElementById('tableTitle');
        
        // Update title
        tableTitle.textContent = title;
        
        // Check if there are users to display
        if (!users || Object.keys(users).length === 0) {
            tableContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No users found in this category</p>
                </div>
            `;
            return;
        }
        
        // Build table HTML
        let tableHTML = `
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Sr. No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="col-hide-mobile">Mobile</th>
                        <th>Status</th>
                        <th class="col-hide-mobile">Last Login</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Add rows
        let srNo = 1;
        for (let key in users) {
            const user = users[key];
            const isMobileUser = mobileIds.includes(parseInt(user.id));
            const statusBadgeClass = isMobileUser ? 'badge-contacted' : 'badge-inactive';
            const statusText = isMobileUser ? 'Mobile Active' : 'Pending Mobile';
            const lastLogin = user.last_logged_in_at ? formatDate(user.last_logged_in_at) : 'Never';
            
            tableHTML += `
                <tr>
                    <td><strong>${srNo}</strong></td>
                    <td>${escapeHtml(user.display_name)}</td>
                    <td>${escapeHtml(user.user_email)}</td>
                    <td class="col-hide-mobile">${escapeHtml(user.user_mobile || 'N/A')}</td>
                    <td>
                        <span class="badge-status ${statusBadgeClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="col-hide-mobile">${lastLogin}</td>
                </tr>
            `;
            srNo++;
        }
        
        tableHTML += `
                </tbody>
            </table>
        `;
        
        // Set container HTML
        tableContainer.innerHTML = tableHTML;
    }

    /**
     * Format date string
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('en-US', options);
        } catch (e) {
            return dateString;
        }
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Optional: Auto-load all users on page load
    // Uncomment the line below if you want the page to show all users initially
    // setTimeout(() => {
    //     const firstCard = document.querySelector('[data-action="show-all-users"]');
    //     if (firstCard) {
    //         firstCard.click();
    //     }
    // }, 100);
});
