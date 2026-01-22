(function (blocks, element, components, serverSideRender) {
  const el = element.createElement;
  const { registerBlockType } = blocks;
  const { PanelBody, ToggleControl, TextControl } = components;

  registerBlockType("bweib-menu/menu-display", {
    edit: function (props) {
      const attrs = props.attributes;
      const setAttributes = props.setAttributes;

      return el(
        "div",
        { className: "bweib-menu-display-editor" },
        el(
          components.InspectorControls,
          {},
          el(
            PanelBody,
            { title: "Menu Display Settings", initialOpen: true },
            el(ToggleControl, {
              label: "Show Filters",
              checked: !!attrs.showFilters,
              onChange: (v) => setAttributes({ showFilters: !!v }),
            }),
            el(ToggleControl, {
              label: "Two Column Desktop",
              checked: !!attrs.twoColumnDesktop,
              onChange: (v) => setAttributes({ twoColumnDesktop: !!v }),
            }),
            el(TextControl, {
              label: "Default Section (slug)",
              value: attrs.defaultSection || "",
              onChange: (v) => setAttributes({ defaultSection: v }),
              help: "Example: shareables, entrees, handhelds",
            })
          )
        ),
        el(serverSideRender, {
          block: "bweib-menu/menu-display",
          attributes: attrs,
        })
      );
    },
    save: function () {
      return null; // dynamic render
    },
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.components,
  window.wp.serverSideRender
);
