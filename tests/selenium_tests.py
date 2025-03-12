from selenium import webdriver

driver = webdriver.Chrome()
driver.get("http://localhost/PESEat/login.php")

email_input = driver.find_element("name", "email")
password_input = driver.find_element("name", "password")
login_button = driver.find_element("tag name", "button")

email_input.send_keys("test@example.com")
password_input.send_keys("password")
login_button.click()

print("Login test completed.")
driver.quit()
