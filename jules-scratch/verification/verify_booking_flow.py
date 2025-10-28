from playwright.sync_api import sync_playwright, expect
import subprocess
import time

def create_user(email, password, role='customer', name=None):
    """Creates a user using Artisan Tinker."""
    if not name:
        name = email.split('@')[0]
    command = f"App\\Models\\User::factory()->create(['name' => '{name}', 'email' => '{email}', 'password' => bcrypt('{password}'), 'role' => '{role}'])"
    subprocess.run(['php', 'reservation-site/artisan', 'tinker', '--execute', command], check=True, capture_output=True)

def run():
    # Clean up and setup database
    subprocess.run(['php', 'reservation-site/artisan', 'migrate:fresh'], check=True, capture_output=True)
    create_user('testuser@example.com', 'password')
    create_user('expert@example.com', 'password', 'expert', 'Dr. Smith')

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        try:
            # 1. Go to homepage and fill the form
            page.goto("http://127.0.0.1:8000/")

            page.get_by_label("Choose an Expert:").select_option(label="Dr. Smith")
            page.get_by_label("Date:").fill("2025-10-31")
            page.get_by_label("Start Time:").fill("10:00")
            page.get_by_label("End Time:").fill("11:00")

            page.get_by_role("button", name="Book Now").click()

            # 2. Assert redirection to login and login
            page.wait_for_url("http://127.0.0.1:8000/login", timeout=10000)

            page.get_by_placeholder("Email").fill("testuser@example.com")
            page.get_by_placeholder("Password").fill("password")
            page.get_by_role("button", name="Sign In").click()

            # 3. Assert redirection to create reservation and check form
            page.wait_for_url("http://127.0.0.1:8000/dashboard", timeout=10000)
            page.goto("http://127.0.0.1:8000/reservations/create")

            # 4. Check that the form is pre-filled
            expert_id = '2' # Assuming expert is the second user created
            expect(page.get_by_label("Choose an Expert:")).to_have_value(expert_id)
            expect(page.get_by_label("Date:")).to_have_value("2025-10-31")
            expect(page.get_by_label("Start Time:")).to_have_value("10:00")
            expect(page.get_by_label("End Time:")).to_have_value("11:00")

            # 5. Take screenshot
            page.screenshot(path="jules-scratch/verification/verification.png")

        except Exception as e:
            print(f"An error occurred: {e}")
            page.screenshot(path="jules-scratch/verification/error.png")
            print(page.content())
        finally:
            browser.close()

if __name__ == "__main__":
    run()
