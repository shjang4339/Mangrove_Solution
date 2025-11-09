<?php
    header("Content-Type: application/json");

    // 업로드 설정
    $upload_dir = '../../image/';
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
    $allowed_extensions = array('jpg', 'jpeg', 'png');

    // 파일이 업로드되었는지 확인
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(array('succ' => false, 'message' => '파일이 선택되지 않았습니다.'));
        exit;
    }

    $file = $_FILES['image'];

    // 업로드 에러 확인
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array('succ' => false, 'message' => '파일 업로드 중 오류가 발생했습니다.'));
        exit;
    }

    // 파일 크기 확인 (5MB 이하)
    if ($file['size'] > $max_file_size) {
        echo json_encode(array('succ' => false, 'message' => '파일 크기는 5MB 이하여야 합니다.'));
        exit;
    }

    // MIME 타입 확인
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        echo json_encode(array('succ' => false, 'message' => '이미지 파일만 업로드 가능합니다. (JPG, PNG)'));
        exit;
    }

    // 파일 확장자 확인
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        echo json_encode(array('succ' => false, 'message' => '허용되지 않은 파일 형식입니다.'));
        exit;
    }

    // 업로드 디렉토리 존재 확인 및 생성
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // 알파벳+숫자 난수 20자리 생성
    function generateRandomFilename($length = 20) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    // 기존 이미지 파일명 확인 (정보 수정 시)
    $current_image = isset($_POST['current_image']) ? $_POST['current_image'] : '';

    // 고유한 파일명 생성 (알파벳+숫자 난수 20자리.jpg)
    $unique_filename = generateRandomFilename(20) . '.jpg';
    $upload_path = $upload_dir . $unique_filename;

    // 동일한 파일명이 존재하는 경우 다시 생성 (매우 드물지만 안전장치)
    while (file_exists($upload_path)) {
        $unique_filename = generateRandomFilename(20) . '.jpg';
        $upload_path = $upload_dir . $unique_filename;
    }

    // 이미지 변환 및 저장 (모두 JPG로 변환)
    try {
        // 원본 이미지 로드
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($file['tmp_name']);
                break;
            default:
                throw new Exception('지원하지 않는 이미지 형식입니다.');
        }

        if ($source_image === false) {
            throw new Exception('이미지를 읽을 수 없습니다.');
        }

        // JPG로 저장 (품질 85)
        if (imagejpeg($source_image, $upload_path, 85)) {
            imagedestroy($source_image);

            // 정보 수정 시 기존 이미지 파일 삭제
            if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }

            echo json_encode(array(
                'succ' => true,
                'message' => '파일이 성공적으로 업로드되었습니다.',
                'image_name' => $unique_filename
            ));
        } else {
            imagedestroy($source_image);
            throw new Exception('파일 저장에 실패했습니다.');
        }
    } catch (Exception $e) {
        echo json_encode(array('succ' => false, 'message' => $e->getMessage()));
    }
?>
