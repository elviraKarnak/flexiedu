jQuery(document).ready(function ($) {

  var ajaxUrl = window.flexiedu_functions ? flexiedu_functions.ajax_url : "";
  var siteURL = window.flexiedu_functions ? flexiedu_functions.siteURL : "";
  var nonce = window.flexiedu_functions ? flexiedu_functions.nonce : "";

      function getBootstrapModal(selector) {
        var modalElement = document.querySelector(selector);

        if (!modalElement || typeof bootstrap === "undefined") {
          return null;
        }

        return bootstrap.Modal.getOrCreateInstance(modalElement);
      }

      function formatBytes(bytes) {
        if (bytes < 1024) {
          return bytes + " B";
        }

        if (bytes < 1048576) {
          return (bytes / 1024).toFixed(1) + " KB";
        }

        return (bytes / 1048576).toFixed(1) + " MB";
      }

      function showFile(file) {
        $("#fileName").text(file.name);
        $("#fileSize").text(formatBytes(file.size));
        $("#filePreview").addClass("show");
        $("#fileError").hide();
      }

      function clearFile() {
        $("#docFile").val("");
        $("#filePreview").removeClass("show");
        $("#fileName").text("-");
        $("#fileSize").text("");
      }

      function showFeaturedImagePreview(src) {
        if (!src) {
          $("#featuredImagePreview").hide();
          $("#featuredImagePreviewImg").attr("src", "");
          return;
        }

        $("#featuredImagePreviewImg").attr("src", src);
        $("#featuredImagePreview").show();
      }

      function clearFeaturedImage() {
        $("#docFeaturedImage").val("");
        showFeaturedImagePreview("");
      }

      function showStatus(type, message) {
        $("#statusStrip")
          .text(message)
          .attr("class", "mt-3 show " + type);

        setTimeout(function () {
          $("#statusStrip").attr("class", "mt-3");
        }, 3500);
      }

      function validateDocumentForm(publishing) {
        var valid = true;

        if (!$.trim($("#docTitle").val())) {
          $("#docTitle").addClass("is-invalid");
          valid = false;
        } else {
          $("#docTitle").removeClass("is-invalid");
        }

        if (publishing && $("#docFile").length && !$("#docFile")[0].files.length) {
          $("#fileError").show();
          valid = false;
        } else {
          $("#fileError").hide();
        }

        return valid;
      }

      function resetEnrollmentButton(button) {
        button
          .removeClass("btn-secondary requested")
          .addClass("btn-primary")
          .prop("disabled", false)
          .text("Request Enrollment");
      }

      function showMainMessage(message, type) {
        $("#flexi-msg").html(
          '<div class="alert alert-' + type + '">' + message + "</div>"
        );
      }

      function showLiveClassMessage(type, message) {
        var messageBox = $("#live-class-form-message");

        if (!messageBox.length) {
          return;
        }

        messageBox
          .removeClass("alert-success alert-danger alert-info d-none")
          .addClass("alert alert-" + type)
          .text(message)
          .show();
      }

      /* templates/organization/pages/dashboard.php */
      $(document).on("click", ".add_course_data", function (e) {
        e.preventDefault();

        var button = $(this);
        var authorId = button.attr("data-author-id");

        button.text("adding..");

        $.ajax({
          url: ajaxUrl,
          type: "POST",
          data: {
            action: "create_course",
            authorId: authorId,
            nonce: nonce,
          },
          success: function (res) {
            if (res.success) {
              var courseId = res.data.course_id;
              window.location.href = siteURL + "/course-builder/" + courseId + "/";
            } else {
              button.text("Add Courses");
              alert(res.data || "Failed to load");
            }
          },
          error: function () {
            button.text("Add Courses");
            alert("Something went wrong");
          },
        });
      });

      /* templates/organization/layout/course-content.php */
      $(document).on("click", ".request-enroll", function () {
        var button = $(this);
        var courseId = button.data("course");
        var orgId = button.data("org");

        if (button.hasClass("requested")) {
          return;
        }

        button.prop("disabled", true).text("Sending...");

        $.ajax({
          url: ajaxUrl,
          type: "POST",
          data: {
            action: "flexi_request_enrollment",
            course_id: courseId,
            org_id: orgId,
            nonce: nonce,
          },
          success: function (res) {
            if (res.success) {
              button
                .addClass("requested")
                .removeClass("btn-primary")
                .addClass("btn-secondary")
                .prop("disabled", false)
                .text("Requested");
            } else {
              resetEnrollmentButton(button);
            }
          },
          error: function () {
            resetEnrollmentButton(button);
          },
        });
      });

      /* templates/organization/layout/org-header.php */
      $(document).on("click", ".page_title-wrap .toggleMenu", function (e) {
        e.preventDefault();
        $(".dashboard-content-wrapper").toggleClass("hideSidebar");

        setTimeout(function () {
          $(".banner-slider").trigger("refresh.owl.carousel");
        }, 300);
      });
  
      // mobile optimize
        $(document).on("click", ".learner-dashboard-left .close-btn", function (e) {
          e.preventDefault();
          $(".dashboard-content-wrapper").toggleClass("hideSidebar");

          setTimeout(function () {
            $(".banner-slider").trigger("refresh.owl.carousel");
          }, 300);
        });

        /* templates/organization/layout/org-banner.php */
        if ($(".owl-carousel").length) {
          setTimeout(function () {
            $(".owl-carousel").trigger("refresh.owl.carousel");
          }, 600);
        }

        /* templates/organization/layout/org-footer.php */
        $(document).on("click", ".edit-doc", function (e) {
          e.preventDefault();

          var postId = $(this).data("id");

          $("#docID").val(postId);

          $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: {
              action: "get_document",
              post_id: postId,
              nonce: nonce,
            },
            success: function (res) {
              if (res.success && res.data) {
                $("#docTitle").val(res.data.title || "");
                $(".doc-types-grid input").prop("checked", false);

                if (Array.isArray(res.data.types)) {
                  res.data.types.forEach(function (slug) {
                    $('.doc-types-grid input[value="' + slug + '"]').prop(
                      "checked",
                      true
                    );
                  });
                }

                if (res.data.file) {
                  $("#fileName").text("Existing file");
                  $("#fileSize").text("");
                  $("#filePreview").addClass("show");
                } else {
                  $("#filePreview").removeClass("show");
                }

                showFeaturedImagePreview(res.data.featured_image || "");

                $("#addDocModalLabel").text("Edit Document");
                $("#publishBtn").text("Update");

                var addDocModal = getBootstrapModal("#addDocModal");
                if (addDocModal) {
                  addDocModal.show();
                }
              } else {
                alert(res.data || "Failed to load document");
              }
            },
            error: function () {
              alert("Server error. Please try again.");
            },
          });
        });

        $("#docFile").on("change", function () {
          if (this.files.length) {
            showFile(this.files[0]);
          }
        });

        $("#docFeaturedImage").on("change", function () {
          var input = this;
          var reader = new FileReader();

          if (!input.files.length) {
            clearFeaturedImage();
            return;
          }

          reader.onload = function (event) {
            showFeaturedImagePreview(event.target.result);
          };

          reader.readAsDataURL(input.files[0]);
        });

        $("#removeFile").on("click", function () {
          clearFile();
        });

        $("#dropZone")
          .on("dragover", function (e) {
            e.preventDefault();
            $(this).addClass("dragover");
          })
          .on("dragleave", function () {
            $(this).removeClass("dragover");
          })
          .on("drop", function (e) {
            e.preventDefault();
            $(this).removeClass("dragover");

            var files = e.originalEvent.dataTransfer.files;
            if (files.length) {
              showFile(files[0]);
            }
          });

          $("#saveDraftBtn").on("click", function () {
            if (validateDocumentForm(false)) {
              showStatus("success", "Draft saved.");
            }
          });

          $("#docTitle").on("input", function () {
            if ($.trim($(this).val())) {
              $(this).removeClass("is-invalid");
            }
          });

          $("#addDocModal").on("hidden.bs.modal", function () {
            if ($("#addDocForm").length) {
              $("#addDocForm")[0].reset();
            }

            $("#addDocForm .is-invalid").removeClass("is-invalid");
            clearFile();
            clearFeaturedImage();
            $("#statusStrip").attr("class", "mt-3");
            $("#addDocModalLabel").text("Add New Document");
            $("#publishBtn").text("Publish");
          });

          $("#publishBtn").on("click", function () {
            var formElement = $("#addDocForm").length ? $("#addDocForm")[0] : null;
            var formData = formElement ? new FormData(formElement) : new FormData();
            var file = $("#docFile").length ? $("#docFile")[0].files[0] : null;
            var featuredImageFile = $("#docFeaturedImage").length
              ? $("#docFeaturedImage")[0].files[0]
              : null;
            var types = [];

            if (!validateDocumentForm(!!file)) {
              return;
            }

            formData.append("action", "add_document");
            formData.append("title", $("#docTitle").val());
            formData.append("post_id", $("#docID").val());
            formData.append("nonce", nonce);

            if (file instanceof File) {
              formData.set("document_file", file, file.name);
            }

            if (featuredImageFile instanceof File) {
              formData.set("featured_image", featuredImageFile, featuredImageFile.name);
            }

            $(".doc-types-grid input:checked").each(function () {
              types.push($(this).val());
            });

            formData.append("doc_types", JSON.stringify(types));

            $.ajax({
              url: ajaxUrl,
              type: "POST",
              data: formData,
              processData: false,
              contentType: false,
              beforeSend: function () {
                $("#publishBtn").prop("disabled", true);
              },
              success: function (res) {
                if (res.success) {
                  showStatus("success", "Document published successfully.");

                  setTimeout(function () {
                    var addDocModal = getBootstrapModal("#addDocModal");
                    if (addDocModal) {
                      addDocModal.hide();
                    }

                    if ($("#addDocForm").length) {
                      $("#addDocForm")[0].reset();
                    }

                    clearFile();
                  }, 1200);

                  location.reload();
                } else {
                  showStatus("error", res.data || "Error occurred");
                }
              },
              error: function () {
                showStatus("error", "Server error");
              },
              complete: function () {
                $("#publishBtn").prop("disabled", false);
              },
            });
          });

          $(document).on("click", ".delete-doc", function () {
            var button = $(this);
            var postId = button.data("id");

            if (!confirm("Are you sure you want to delete this document?")) {
              return;
            }

            $.ajax({
              url: ajaxUrl,
              type: "POST",
              data: {
                action: "delete_document",
                post_id: postId,
                nonce: nonce,
              },
              beforeSend: function () {
                button.prop("disabled", true).text("Deleting...");
              },
              success: function (res) {
                if (res.success) {
                  button.closest(".doc-card").fadeOut(300, function () {
                    $(this).remove();
                  });
                } else {
                  alert(res.data || "Delete failed");
                  button.prop("disabled", false).text("Delete");
                }
              },
              error: function () {
                alert("Server error");
                button.prop("disabled", false).text("Delete");
              },
            });
          });

          /* templates/organization/pages/profile.php */
          $('input[name="profile_image"]').on("change", function () {
            var input = this;
            var reader = new FileReader();

            if (!input.files.length) {
              return;
            }

            reader.onload = function (event) {
              $("#profile-preview").attr("src", event.target.result);
            };

            reader.readAsDataURL(input.files[0]);
          });

          $("#profile-form").on("submit", function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("action", "flexi_save_profile");
            formData.append("nonce", nonce);

            $.ajax({
              url: ajaxUrl,
              type: "POST",
              data: formData,
              contentType: false,
              processData: false,
              beforeSend: function () {
                $("#profile-msg").html("<p>Saving...</p>");
              },
              success: function (response) {
                if (response.success) {
                  $("#profile-msg").html(
                    '<p style="color:green;">' + response.data + "</p>"
                  );
                  window.location.reload();
                } else {
                  $("#profile-msg").html(
                    '<p style="color:red;">' + response.data + "</p>"
                  );
                }
              },
            });
          });

          /* templates/organization/pages/receivedapplication.php */
          $(document).on("click", ".accept-application", function () {
            var rowId = $(this).data("row-id");

            $.ajax({
              url: ajaxUrl,
              type: "POST",
              data: {
                action: "accept_application",
                rowId: rowId,
                nonce: nonce,
              },
              success: function (res) {
                if (res.success) {
                  window.location.reload();
                } else {
                  alert(res.data || "Failed to load");
                }
              },
            });
          });

          $(document).on("click", ".reject-application", function () {
            var rowId = $(this).data("row-id");
            $("#row_id").val(rowId);

            var rejectModal = getBootstrapModal("#formRejectModal");
            if (rejectModal) {
              rejectModal.show();
            }
          });

          $(".reject_application").on("submit", function (e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
              url: ajaxUrl,
              type: "POST",
              data: formData,
              success: function (res) {
                if (res.success) {
                  window.location.reload();
                } else {
                  alert(res.data || "Failed to load");
                }
              },
            });
          });

          if ($("#receivedApplicationsTable").length) {
                $("#receivedApplicationsTable").DataTable();
          }

          if ($("#receivedApplicationsTable").length) {
              $("#receivedApplicationsTable").DataTable();
        }

        /* views/group-registration.php */
        $("#flexi-register-form").on("submit", function (e) {
          e.preventDefault();

          var formData = new FormData(this);
          formData.append("action", "flexi_register_user");

          showMainMessage("Processing...", "info");

          $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
              if (res.success) {
                showMainMessage(res.data.message, "success");

                setTimeout(function () {
                  window.location.href = res.data.redirect;
                }, 1000);
              } else {
                showMainMessage(res.data, "danger");
              }
            },
            error: function () {
              showMainMessage("Something went wrong", "danger");
            },
          });
        });

        /* templates/organization/pages/add-live-classes.php */
        $("#flexiedu-live-class-form").on("submit", function (e) {
          e.preventDefault();

          var form = $(this);
          var data = form.serialize();

          data += "&action=flexiedu_create_live_class";
          data +=
            "&nonce=" +
            encodeURIComponent(
              window.flexiedu_live_class && flexiedu_live_class.nonce
                ? flexiedu_live_class.nonce
                : nonce
            );

            $this = $(this).find('button[type="submit"]');
            $this.prop("disabled", true).text("Please wait...");

          $.ajax({
            url:
              window.flexiedu_live_class && flexiedu_live_class.ajax_url
                ? flexiedu_live_class.ajax_url
                : ajaxUrl,
            type: "POST",
            data: data,
            success: function (response) {
              location.reload();
            },
            error: function (xhr) {
              console.log(xhr.responseText);
            },
          });
        });

        /* templates/organization/pages/add-coursebundle.php */
        function getSelectedCourseBundleCourses() {
          return $(".course-bundle-course:checked")
            .map(function () {
              return $(this).val();
            })
            .get();
        }

        $("#flexiedu-course-bundle-form").on("submit", function (e) {
          e.preventDefault();

          var form = $(this);
          var messageDiv = $("#course-bundle-form-message");
          var courseList = $("#course_bundle_courses");

          // Clear previous messages
          messageDiv.hide().html("");
          courseList.removeClass("is-invalid");

          // Check if courses are selected
          var selectedCourses = getSelectedCourseBundleCourses();
          if (!selectedCourses || selectedCourses.length === 0) {
            courseList.addClass("is-invalid");
          }

          // Validate form
          if (!form[0].checkValidity() || !selectedCourses || selectedCourses.length === 0) {
            form[0].classList.add("was-validated");
            return;
          }

          // Prepare form data
          var formData = new FormData(form[0]);
          formData.append("action", "flexiedu_create_course_bundle");
          formData.append("nonce", nonce);

          // Convert course_ids array to JSON string
          formData.delete("course_ids[]");
          formData.set("course_ids", JSON.stringify(selectedCourses));

          // Show loading state
          var submitBtn = form.find('button[type="submit"]');
          var originalText = submitBtn.text();
          submitBtn.prop("disabled", true).text("Creating...");

          $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
              if (response.success) {
                messageDiv.html('<div class="alert alert-success">' + (response.data.message || "Course bundle created successfully!") + '</div>').show();

                // Reset form
                form[0].reset();
                form[0].classList.remove("was-validated");

                // Optionally redirect or reload
                setTimeout(function () {
                  window.location.reload();
                }, 2000);
              } else {
                messageDiv.html('<div class="alert alert-danger">' + (response.data || "Failed to create course bundle.") + '</div>').show();
              }
            },
            error: function (xhr, status, error) {
              messageDiv.html('<div class="alert alert-danger">Server error occurred. Please try again.</div>').show();
              console.error("AJAX Error:", xhr.responseText);
            },
            complete: function () {
              submitBtn.prop("disabled", false).text(originalText);
            }
          });
        });

        // Reset custom validity on course selection change
        $(".course-bundle-course").on("change", function () {
          if (getSelectedCourseBundleCourses().length > 0) {
            $("#course_bundle_courses").removeClass("is-invalid");
          }
        });


        jQuery(document).on('click', '.open-create-bundle', function () {

          // Reset form fields
          jQuery('#flexiedu-course-bundle-form')[0].reset();

          // Clear hidden ID
          jQuery('#bundle_id').val('');

          // Uncheck all courses
          jQuery('.course-bundle-course').prop('checked', false);

          // Reset title
          jQuery('#addBundlesTitle').text('Create Bundle');

          // Reset button text
          jQuery('#flexiedu-course-bundle-form button[type="submit"]').text('Create Bundle');

      });


      
        jQuery(document).on('click', '.addlive-classmodal', function () {

          // Reset form fields
          jQuery('#flexiedu-live-class-form')[0].reset();

          // Clear hidden ID
          jQuery('#class_id').val('');

          // Uncheck all courses
         // jQuery('.course-bundle-course').prop('checked', false);

          // Reset title
          jQuery('#addliveClassModalTitle').text('Add Live Classes');

          // Reset button text
          jQuery('.live-course-edit').text('Create Live Class');

      });


      

      jQuery(document).on('click', '.edit-bundle-btn', function () {

          var bundle_id = jQuery(this).data('id');

          jQuery.ajax({
              url: flexiedu_functions.ajax_url,
              type: 'POST',
              data: {
                  action: 'get_bundle',
                  bundle_id: bundle_id
              },
              success: function (res) {

                  if (res.success) {

                      let data = res.data;

                      // Set values
                      jQuery('#bundle_id').val(data.id);
                      jQuery('#course_bundle_title').val(data.title);
                      jQuery('#course_bundle_description').val(data.description);

                      // Reset all checkboxes
                      jQuery('.course-bundle-course').prop('checked', false);

                      // Check selected courses
                      if (data.course_ids) {
                          data.course_ids.forEach(function (id) {
                              jQuery('#course_bundle_course_' + id).prop('checked', true);
                          });
                      }

                      // Change title/button
                      jQuery('#addBundlesTitle').text('Update Bundle');
                      jQuery('#flexiedu-course-bundle-form button[type="submit"]').text('Update Bundle');

                      // Open modal
                      var modal = new bootstrap.Modal(document.getElementById('addBundles'));
                      modal.show();

                  } else {
                      alert(res.data.message);
                  }
              }
          });

      });

      jQuery(document).on('click', '.btn-delete', function () {

        let bundle_id = jQuery(this).data('id');

        if (!confirm('Are you sure you want to delete this bundle?')) {
            return;
        }

        jQuery.ajax({
            url: flexiedu_functions.ajax_url,
            type: 'POST',
            data: {
                action: 'flexiedu_delete_course_bundle',
                bundle_id: bundle_id,
                nonce: flexiedu_functions.nonce
            },
            success: function (res) {

              console.log(res);

                if (res.success) {
                    alert(res.data.message);

                    // remove card from UI
                    // jQuery('[data-id="'+bundle_id+'"]').closest('.learner-content__card').remove();
                    location.reload();

                } else {
                    alert(res.data);
                    console.error(res);
                }
            }
        });

    });



      jQuery(document).on('click', '.edit-class', function () {

          var classId = jQuery(this).attr('data-row-id');

          console.log('Editing class with ID:', classId);

          jQuery.ajax({
              url: ajaxUrl,
              type: 'POST',
              data: {
                  action: 'edit_class_by_id',
                  class_id: classId,
                  nonce:nonce
              },
              success: function (res) {

                  if (res.success) {

                      let data = res.data['0'];

                      console.log('Class data received:', data);

                      // Set values
                      jQuery('#class_id').val(classId);
                      jQuery('#live_class_title').val(data.title);
                      jQuery('#live_class_description').val(data.description);
                      jQuery('#live_class_event_color').val(data.color);
                      jQuery('#live_class_webinar_link').val(data.webinar_link);

                    jQuery('#live_class_start_date_time').val(new Date(data.start).toISOString().slice(0,16));
                    jQuery('#live_class_end_date_time').val(new Date(data.end).toISOString().slice(0,16));

                      // Reset all checkboxes
                      jQuery('#live_class_course').val(data.course_id);

                      // Change title/button
                      jQuery('#addliveClassModalTitle').text('Update Live Class');
                      jQuery('.live-course-edit').text('Update Class');

                      // // Open modal
                      var modal = new bootstrap.Modal(document.getElementById('addliveClassModal'));
                      modal.show();

                  } else {
                      alert(res.data.message);
                  }
              }
          });

      });


      jQuery(document).on('click', '.delete-class', function () {
          var classId = jQuery(this).attr('data-row-id');   

          if (!confirm('Are you sure you want to delete this class?')) {
              return;
          }
          jQuery(this).prop('disabled', true).text('Deleting...');
            jQuery.ajax({
                url: ajaxUrl, 
                type: 'POST',
                data: {
                    action: 'delete_class_by_id',
                    class_id: classId,
                    nonce:nonce,
                },
                success: function (res) {
                    if (res.success) {
                        alert(res.data.message);
                        location.reload();
                    } else {
                        alert(res.data);
                        console.error(res);
                    }             
                  }
            });


      jQuery(document).on('submit', '#featureBox', function (e) {

          e.preventDefault();

          let form = jQuery(this);
          let formData = {};

          // 🔹 Get org_id
          formData.org_id = jQuery("#org_id").val();

          // 🔹 Loop all checkboxes
          form.find("input[type='checkbox']").each(function () {
              let name = jQuery(this).attr("name");
              formData[name] = jQuery(this).is(":checked") ? 1 : 0;
          });

          console.log("FORM DATA:", formData);

    });
                

        

      });






});

// document ready end

jQuery(window).on("load", function () {
  if (typeof jQuery === "undefined") {
    return;
  }

  /* templates/organization/layout/org-footer.php */
  jQuery(document).trigger("learndash-init");

  jQuery(".ld-expand-button")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();

      var container = jQuery(this).closest(".ld-item-list-item");
      container.toggleClass("ld-expanded");
      container.find(".ld-item-children").slideToggle(200);
    });

  jQuery(".ld-expand-all")
    .off("click")
    .on("click", function () {
      var button = jQuery(this);
      var expanded = button.hasClass("ld-expanded");

      if (expanded) {
        jQuery(".ld-item-list-item")
          .removeClass("ld-expanded")
          .find(".ld-item-children")
          .slideUp();
      } else {
        jQuery(".ld-item-list-item")
          .addClass("ld-expanded")
          .find(".ld-item-children")
          .slideDown();
      }

      button.toggleClass("ld-expanded");
    });
});