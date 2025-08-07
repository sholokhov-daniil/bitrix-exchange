<script setup>
import {defineProps, defineEmits, watch} from 'vue';

const props = defineProps({
  show: {type: Boolean, default: () => false}
})

const emit = defineEmits(['opened', 'closed'])

const popupId = 'sholokhov-exchange-detail-map-create';

watch(
    () => props.show,
    (show) => {
      if (show) {
        console.log(exist(), 'SHOW')

        if (exist()) {
          open();
        } else {
          create();
          open();
        }

        open();
      } else {
        close();
      }
    }
);

const close = () => {
  BX.PopupWindowManager.getPopupById(popupId).close();
  emit('closed');
};

const open = () => {
  BX.PopupWindowManager.getPopupById(popupId).show();
  emit('opened');
}

const create = () => {
  BX.PopupWindowManager.create(
      popupId,
      null,
      {
        content: `<div id="${popupId}__content"></div>`,
        closeIcon: {right: "20px", top: "10px" },
        titleBar: {
          content: BX.create(
              "span",
              {
                html: '<b>Добавление поля</b>',
                props: {
                  className: 'access-title-bar'
                }
              }
          )
        },
        darkMode: true,
        autoHide: true,
        buttons: [
          new BX.PopupWindowButton({
            text: "Сохранить" ,
            className: "popup-window-button-accept" ,
            events: {click: function(){
                this.popupWindow.close();
              }}
          }),
        ]
      }
  );
}

const exist = () => BX.PopupWindowManager.isPopupExists(popupId);
</script>

<template>
  <Teleport v-if="show" :to="`#${popupId}__content`">
    TEST
  </Teleport>
</template>