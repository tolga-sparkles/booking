from playwright.sync_api import sync_playwright, expect
import subprocess

def run():
    # 1. Seed the database
    subprocess.run(['php', 'reservation-site/artisan', 'migrate:fresh', '--seed'], check=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        try:
            # 2. Login as admin
            page.goto("http://127.0.0.1:8000/login")
            page.get_by_label("Email").fill("admin@example.com")
            page.get_by_label("Password").fill("password")
            page.get_by_role("button", name="Log in").click()
            expect(page).to_have_url("http://127.0.0.1:8000/dashboard")

            # Print page content for debugging
            print(page.content())

            # 3. Navigate to admin panel and view details
            page.get_by_role("link", name="Admin Panel").click()
            expect(page).to_have_url("http://127.0.0.1:8000/admin/reservations")

            # ... rest of the script

        except Exception as e:
            print(f"An error occurred: {e}")
            page.screenshot(path="jules-scratch/verification/error.png")
        finally:
            browser.close()

if __name__ == "__main__":
    run()
