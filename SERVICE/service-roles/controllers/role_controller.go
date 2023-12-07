package controllers

import (
	"log"
	"net/http"
	"time"

	"github.com/hiskiaphsp/service-role/models"
	"github.com/hiskiaphsp/service-role/repositories"
	"github.com/labstack/echo/v4"
)

type RoleController struct {
	RoleRepository *repositories.RoleRepository
}

func (c *RoleController) CreateRole(ctx echo.Context) error {
	role := new(models.Role)
	if err := ctx.Bind(role); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	role.CreatedAt = time.Now().Unix()

	err := c.RoleRepository.Create(role)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to create role")
	}

	return ctx.JSON(http.StatusCreated, role)
}

func (c *RoleController) GetAllRoles(ctx echo.Context) error {
	roles, err := c.RoleRepository.GetAll()
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get roles")
	}

	return ctx.JSON(http.StatusOK, roles)
}

func (c *RoleController) GetRoleByID(ctx echo.Context) error {
	id := ctx.Param("id")

	role, err := c.RoleRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusNotFound, "Role not found")
	}

	return ctx.JSON(http.StatusOK, role)
}

func (c *RoleController) UpdateRole(ctx echo.Context) error {
	id := ctx.Param("id")

	previousRole, err := c.RoleRepository.GetByID(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to get role")
	}

	role := new(models.Role)
	if err := ctx.Bind(role); err != nil {
		return echo.NewHTTPError(http.StatusBadRequest, err.Error())
	}

	role.CreatedAt = previousRole.CreatedAt

	err = c.RoleRepository.Update(id, role)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to update role")
	}

	return ctx.JSON(http.StatusOK, role)
}

func (c *RoleController) DeleteRole(ctx echo.Context) error {
	id := ctx.Param("id")

	err := c.RoleRepository.Delete(id)
	if err != nil {
		log.Println(err)
		return echo.NewHTTPError(http.StatusInternalServerError, "Failed to delete role")
	}

	return ctx.NoContent(http.StatusNoContent)
}
