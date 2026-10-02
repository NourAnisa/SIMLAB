import os

files = [
    r'c:\xampp\htdocs\SIMLAB\tests\Unit\Controllers\MahasiswaSertifikasiControllerTest.php',
    r'c:\xampp\htdocs\SIMLAB\tests\Unit\Controllers\MahasiswaSuratBebasLabControllerTest.php',
    r'c:\xampp\htdocs\SIMLAB\tests\Unit\Controllers\MahasiswaIzinPenelitianControllerTest.php',
    r'c:\xampp\htdocs\SIMLAB\tests\Unit\Controllers\PeminjamanLabControllerTest.php',
]

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    # Replace user creations to include profile_completed
    content = content.replace("['role' => 'mahasiswa']", "['role' => 'mahasiswa', 'profile_completed' => true]")
    content = content.replace("['role' => 'admin']", "['role' => 'admin', 'profile_completed' => true]")
    content = content.replace("['role' => 'kepala_lab']", "['role' => 'kepala_lab', 'profile_completed' => true]")
    content = content.replace("User::factory()->create()", "User::factory()->create(['profile_completed' => true])")
    
    with open(f, 'w') as file:
        file.write(content)

print('User test profile replaced successfully')
