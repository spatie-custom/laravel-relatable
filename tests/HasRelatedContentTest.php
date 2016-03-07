<?php

namespace Spatie\Relatable\Test;

use Spatie\Relatable\Test\TestModels\{ HasFruitAsRelatedContent, Lime };
use Spatie\Relatable\Relatable;
use Spatie\Relatable\Test\TestModels\Strawberry;

class HasRelatedContentTest extends TestCase
{
    /** @test */
    function it_can_add_a_related_model_via_a_model_instance()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);

        $hasFruit->relate($lime);
        $this->assertModelIsRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_can_add_a_related_model_via_an_id_and_a_type()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);

        $hasFruit->relate(1, Lime::class);
        $this->assertModelIsRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_cant_add_a_related_model_via_id_if_no_type_is_provided()
    {
        $this->expectException(\InvalidArgumentException::class);

        HasFruitAsRelatedContent::find(1)->relate(1);
    }

    /** @test */
    function it_can_remove_a_related_model_via_a_model_instance()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);

        $hasFruit->relate($lime);
        $this->assertModelIsRelatedToSource($lime, $hasFruit);

        $hasFruit->unrelate($lime);
        $this->assertModelIsntRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_can_remove_a_related_model_via_an_id_and_a_type()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);

        $hasFruit->relate(1, Lime::class);
        $this->assertModelIsRelatedToSource($lime, $hasFruit);

        $hasFruit->unrelate(1, Lime::class);
        $this->assertModelIsntRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_can_retrieve_a_collection_of_its_related_content()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);
        $strawberry = Strawberry::find(1);

        $hasFruit->relate($lime);
        $hasFruit->relate($strawberry);

        $related = $hasFruit->related;

        $this->assertCount(2, $related);
        $this->assertRelatedCollectionContains($related, $lime);
        $this->assertRelatedCollectionContains($related, $strawberry);
    }

    /** @test */
    function it_can_determine_if_it_has_related_content()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);

        $this->assertFalse($hasFruit->hasRelated());
    }

    /** @test */
    function it_can_determine_if_it_doenst_have_related_content()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $hasFruit->relate(Lime::find(1));

        $this->assertTrue($hasFruit->hasRelated());
    }

    /** @test */
    function it_can_sync_related_content_from_a_collection_of_models()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);
        $strawberry = Strawberry::find(1);

        $hasFruit->relate($lime);

        $hasFruit->syncRelated(collect([$strawberry]));

        $related = $hasFruit->related;

        $this->assertCount(1, $related);
        $this->assertModelIsRelatedToSource($strawberry, $hasFruit);
        $this->assertModelIsntRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_can_sync_related_content_from_an_array_of_types_and_ids()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);
        $strawberry = Strawberry::find(1);

        $hasFruit->relate($lime);

        $hasFruit->syncRelated([['id' => 1, 'type' => Strawberry::class]]);

        $related = $hasFruit->related;

        $this->assertCount(1, $related);
        $this->assertModelIsRelatedToSource($strawberry, $hasFruit);
        $this->assertModelIsntRelatedToSource($lime, $hasFruit);
    }

    /** @test */
    function it_can_sync_related_content_without_detaching()
    {
        $hasFruit = HasFruitAsRelatedContent::find(1);
        $lime = Lime::find(1);
        $strawberry = Strawberry::find(1);

        $hasFruit->relate($lime);

        $hasFruit->syncRelated(collect([$strawberry]), false);

        $related = $hasFruit->loadRelated();

        $this->assertCount(2, $related);
        $this->assertModelIsRelatedToSource($strawberry, $hasFruit);
        $this->assertModelIsRelatedToSource($lime, $hasFruit);
    }
}
