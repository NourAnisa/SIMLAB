import os
import sys

base_path = r'c:\xampp\htdocs\SIMLAB\tests\Feature'
files_to_delete = [
    'IzinPenelitianAdminTest.php',
    'IzinPenelitianKepalaLabTest.php',
    'IzinPenelitianMahasiswaTest.php',
    'PeminjamanLabAdminTest.php',
    'PeminjamanLabDosenTest.php',
    'PeminjamanLabKepalaLabTest.php',
    'PeminjamanLabMahasiswaTest.php',
    'SuratBebasLabAdminTest.php',
    'SuratBebasLabKepalaLabTest.php',
    'SuratBebasLabMahasiswaTest.php',
    'SertifikasiMahasiswaTest.php',
    'SertifikasiAdminTest.php',
    'SertifikasiKepalaLabTest.php',
    'KepalaLabPeminjamanLabTest.php',
    'KepalaLabIzinPenelitianTest.php',
    'KepalaLabSertifikasiTest.php',
    'KepalaLabSuratBebasLabTest.php',
    'DosenModulTest.php',
    'DosenPeminjamanLabTest.php',
    'ProfileTest.php'
]

deleted = []
not_found = []

for filename in files_to_delete:
    file_path = os.path.join(base_path, filename)
    try:
        if os.path.exists(file_path):
            os.remove(file_path)
            deleted.append(filename)
            print(f'DELETED: {filename}')
        else:
            not_found.append(filename)
            print(f'NOT FOUND: {filename}')
    except Exception as e:
        not_found.append(filename)
        print(f'ERROR: {filename} - {str(e)}')

print('')
print('='*70)
print(f'SUMMARY: {len(deleted)} deleted, {len(not_found)} not found')
print('='*70)

if deleted:
    print(f'\nDeleted ({len(deleted)}):')
    for f in deleted:
        print(f'  {f}')

if not_found:
    print(f'\nNot found ({len(not_found)}):')
    for f in not_found:
        print(f'  {f}')
