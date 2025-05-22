<?php
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../helpers/ApiResponse.php';
require_once __DIR__ . '/../helpers/AdminMiddleware.php';

class DepartmentsController
{
    public static function handleRequest($method, $segments)
    {
        switch ($method) {
            case 'GET':
                if (empty($segments[1])) {
                    self::getAllDepartments();
                } else {
                    $departmentId = $segments[1];
                    self::getDepartment($departmentId);
                }
                break;

            case 'POST':
                self::createDepartment();
                break;

            case 'PUT':
                if (!empty($segments[1])) {
                    $departmentId = $segments[1];
                    self::updateDepartment($departmentId);
                }
                break;

            case 'DELETE':
                if (!empty($segments[1])) {
                    $departmentId = $segments[1];
                    self::deleteDepartment($departmentId);
                }
                break;

            default:
                ApiResponse::error('Method not allowed', 405);
        }
    }

    private static function getAllDepartments()
    {
        $department = new Department();
        $departments = $department->getAll();
        ApiResponse::success($departments);
    }

    private static function getDepartment($id)
    {
        $department = new Department();
        $result = $department->find($id);
        if ($result) {
            ApiResponse::success($result);
        }
        ApiResponse::error('Department not found', 404);
    }

    private static function createDepartment()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            ApiResponse::error('Name is required', 400);
        }

        $department = new Department();
        $existing = $department->findByName($data['name']);
        if ($existing) {
            ApiResponse::error('Department already exists', 409);
        }

        $id = $department->create($data['name']);
        ApiResponse::success(['id' => $id], 'Department created', 201);
    }

    private static function updateDepartment($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name'])) {
            ApiResponse::error('Name is required', 400);
        }

        $department = new Department();
        $existing = $department->find($id);
        if (!$existing) {
            ApiResponse::error('Department not found', 404);
        }

        $updated = $department->update($id, $data['name']);
        ApiResponse::success(['affected_rows' => $updated]);
    }

    private static function deleteDepartment($id)
    {
        $department = new Department();
        $existing = $department->find($id);
        if (!$existing) {
            ApiResponse::error('Department not found', 404);
        }

        $deleted = $department->delete($id);
        ApiResponse::success(['affected_rows' => $deleted]);
    }
}
