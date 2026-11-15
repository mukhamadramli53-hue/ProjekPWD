<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="app-base" content="<?= e(app_url()) ?>">
    <title><?= e($pageTitle) ?> | Kopi Cirebon — Argon Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Open Sans"', 'sans-serif'],
                    },
                    colors: {
                        argon: {
                            blue: '#5e72e4',
                            indigo: '#5603ad',
                            purple: '#8965e0',
                            pink: '#f3a4b5',
                            red: '#f5365c',
                            orange: '#fb6340',
                            yellow: '#ffd600',
                            green: '#2dce89',
                            teal: '#11cdef',
                            cyan: '#2bffc6',
                            dark: '#172b4d',
                            slate: '#8392ab',
                            light: '#f8f9fa'
                        }
                    },
                    boxShadow: {
                        'argon-sm': '0 2px 4px rgba(0,0,0,0.05)',
                        'argon': '0 7px 14px rgba(50,50,93,0.1), 0 3px 6px rgba(0,0,0,0.08)',
                        'argon-xl': '0 15px 35px rgba(50,50,93,0.1), 0 5px 15px rgba(0,0,0,0.07)',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= e(app_url('assets/css/app.css')) ?>">
</head>
<body class="m-0 font-sans antialiased font-normal text-start text-base leading-default text-slate-600 bg-slate-100 min-h-screen relative overflow-x-hidden">
